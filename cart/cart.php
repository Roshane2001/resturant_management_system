<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    header('location:./../auth/login.php');
    exit;
}

// Fetch Categories for Tabs
include('fetch_categories.php');

// Store categories in array for multiple iteration
$cats = [];
foreach ($categories as $cat) {
    $cats[] = $cat;
}

// Fetch Parent Categories for JS
$parent_categories_list = [];
$pcat_query = mysqli_query($con, "SELECT * FROM tblparentcategory");
while ($pcat_row = mysqli_fetch_assoc($pcat_query)) {
    $parent_categories_list[] = $pcat_row;
}

// Fetch Service Charge
include('fetch_service_charge.php');

// Fetch tables for JS
$js_tables = [];
$js_tbl_query = mysqli_query($con, "SELECT * FROM tbltables");
while ($row = mysqli_fetch_assoc($js_tbl_query)) {
    $js_tables[] = $row;
}

// Get initial max ID for polling tblorder_details
$max_query = mysqli_query($con, "SELECT MAX(ID) as max_id FROM tblorder_details");
$max_row = mysqli_fetch_assoc($max_query);
$initial_max_id = $max_row['max_id'] ? $max_row['max_id'] : 0;

// Get current counts for badges
$count_query = mysqli_query($con, "
    SELECT 
        (SELECT COUNT(DISTINCT OrderID, KOT) FROM tblorder_details WHERE order_status = 0) as pending_count,
        (SELECT COUNT(DISTINCT OrderID, KOT) FROM tblorder_details WHERE order_status = 1) as processing_count
");
$counts = mysqli_fetch_assoc($count_query);
$initial_pending = $counts['pending_count'] ? intval($counts['pending_count']) : 0;
$initial_processing = $counts['processing_count'] ? intval($counts['processing_count']) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    
    <title>RMS - POS</title>
    <?php include_once('../include/header.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom POS Styles */
        body { overflow: hidden; } /* Prevent page scroll */
        #wrapper #content-wrapper { background-color: #f8f9fc; }
        .container-fluid { padding: 0; height: calc(100vh - 70px); }
        .pos-row { height: 100%; margin: 0; }
        
        /* Left Panel - Order Summary */
        .left-panel { background: white; border-right: 1px solid #e3e6f0; display: flex; flex-direction: column; height: 100%; padding: 0; }
        .order-header { padding: 15px; border-bottom: 1px solid #e3e6f0; }
        .dine-in-badge { background: #e74a3b; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; }
        .order-info { font-size: 0.85rem; color: #858796; margin-top: 5px; }
        .order-list-area { flex: 1; overflow-y: auto; padding: 0; }
        .order-item { padding: 10px 15px; border-bottom: 1px solid #eaecf4; cursor: pointer; }
        .order-item:hover { background-color: #f8f9fc; }
        .order-item.active { background-color: #eaecf4; }
        .order-footer { padding: 15px; border-top: 1px solid #e3e6f0; background: #fff; }
        .total-display { font-size: 1.5rem; font-weight: 800; color: #4e73df; text-align: right; }
        .action-buttons .btn { border-radius: 50%; width: 45px; height: 45px; display: inline-flex; align-items: center; justify-content: center; margin: 0 5px; }

        /* Right Panel - Menu & Keypad */
        .right-panel { display: flex; flex-direction: column; height: 100%; padding: 0; }
        
        /* Tabs */
        .menu-tabs { background: #2c3e50; padding: 10px 5px 0 5px; overflow-x: auto; white-space: nowrap; flex-shrink: 0; }
        .menu-tabs::-webkit-scrollbar { height: 5px; }
        .menu-tabs::-webkit-scrollbar-thumb { background: #5a5c69; border-radius: 5px; }
        .nav-pills .nav-link { color: #d1d3e2; border-radius: 10px 10px 0 0; margin-right: 5px; padding: 10px 20px; font-weight: 600; }
        .nav-pills .nav-link.active { background-color: #e74a3b; color: white; }

        /* Parent Categories */
        .parent-tabs { background: #1a252f; padding: 8px 5px 0 5px; overflow-x: auto; white-space: nowrap; flex-shrink: 0; border-bottom: 1px solid #2c3e50; }
        .parent-tabs::-webkit-scrollbar { height: 3px; }
        .parent-tabs::-webkit-scrollbar-thumb { background: #4e73df; border-radius: 3px; }
        .parent-tabs .nav-link { color: #d1d3e2; border-radius: 20px; margin-right: 5px; padding: 5px 15px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .parent-tabs .nav-link.active { background-color: #4e73df; color: white; box-shadow: 0 2px 5px rgba(78, 115, 223, 0.4); }

        /* Food Grid */
        .food-grid-container { flex: 1; overflow-y: auto; padding: 15px; background: #eaecf4; }
        .food-card { 
            height: 100px; 
            border-radius: 15px; 
            /*box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); */
            color: white; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            padding: 12px; 
            cursor: pointer; 
            transition: all 0.1s;
            border: none;
            width: 100%;
            text-align: left;
            position: relative;
            overflow: visible;
        }
        .food-card:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.25); }
        .food-card:active { transform: scale(0.95); }
        .item-price { font-weight: bold; background: rgba(0,0,0,0.2); padding: 2px 8px; border-radius: 10px; align-self: flex-end; }
        
        /* Category Colors */
        .cat-color-0 { background: linear-gradient(45deg, #4e73df, #224abe); }
        .cat-color-1 { background: linear-gradient(45deg, #1cc88a, #13855c); }
        .cat-color-2 { background: linear-gradient(45deg, #f6c23e, #dda20a); }
        .cat-color-3 { background: linear-gradient(45deg, #e74a3b, #be2617); }
        .cat-color-4 { background: linear-gradient(45deg, #858796, #60616f); }

        /* Billing Controls & Keypad */
        .billing-section { background: white; padding: 10px; border-top: 1px solid #e3e6f0; height: 35%; display: flex; flex-direction: column; }
        .change-display { font-size: 1.2rem; font-weight: bold; color: #1cc88a; margin-bottom: 5px; display: flex; justify-content: space-between; }
        .keypad-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; flex: 1; }
        .keypad-btn { 
            border: none; border-radius: 8px; font-weight: bold; font-size: 1.1rem; 
            box-shadow: 0 2px 0 rgba(0,0,0,0.05); transition: all 0.1s;
            display: flex; align-items: center; justify-content: center;
        }
        .keypad-btn:active { transform: translateY(2px); box-shadow: none; }
        .btn-num { background: #f8f9fc; color: #5a5c69; border: 1px solid #d1d3e2; }
        .btn-func { background: #4e73df; color: white; font-size: 0.9rem; }
        .btn-red { background: #e74a3b; color: white; }
        .btn-yellow { background: #f6c23e; color: white; }
        .btn-green { background: #1cc88a; color: white; }
        .btn-big { grid-row: span 2; }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="row pos-row">
                        <!-- Left Panel (Order Summary) -->
                        <div class="col-md-4 left-panel">
                            <div class="order-header">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="dine-in-badge">DINE IN</span>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle px-3" type="button" id="tableSelectDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Table Select
                                        </button>
                                        <div class="dropdown-menu" id="tableSelectMenu" aria-labelledby="tableSelectDropdown">
                                            <?php
                                            $sql = "SELECT t.ID as TableID, t.TableName, o.ID as OrderID, o.OrderType 
                                                    FROM tblorder o 
                                                    LEFT JOIN tbltables t ON o.TableID = t.ID 
                                                    WHERE o.Status = 'Pending' 
                                                    ORDER BY o.ID DESC";
                                            $tbl_query = mysqli_query($con, $sql);

                                            if (mysqli_num_rows($tbl_query) > 0) {
                                                while ($tbl_row = mysqli_fetch_assoc($tbl_query)) {
                                                    $displayName = ($tbl_row['OrderType'] === 'Take-away') ? "Take Away #" . $tbl_row['OrderID'] : $tbl_row['TableName'];
                                                    $tableId = $tbl_row['TableID'] ? $tbl_row['TableID'] : 0;
                                                    echo '<a class="dropdown-item" href="javascript:void(0)" onclick="selectTable(\'' . addslashes($displayName) . '\', ' . $tableId . ', ' . $tbl_row['OrderID'] . ')">' . htmlspecialchars($displayName) . ' - Bill #' . $tbl_row['OrderID'] . '</a>';
                                                }
                                            } else {
                                                echo '<span class="dropdown-item text-muted">No pending tables</span>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" id="btn-dine-in" class="btn btn-danger active px-3" onclick="setOrderType('Dine-in')">
                                            <i class="fas fa-utensils"></i>
                                        </button>
                                        <button type="button" id="btn-take-away" class="btn btn-outline-secondary px-3" onclick="setOrderType('Take-away')">
                                            <i class="fas fa-shopping-bag"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center order-info">
                                    <span><i class="fas fa-user-circle"></i> <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'User'; ?></span>
                                    <span id="current-time"></span>
                                </div>
                            </div>

                            <div class="order-list-area" id="order-list">
                                <!-- Order items will be injected here via JS -->
                                <div class="text-center mt-5 text-gray-400">
                                    <i class="fas fa-shopping-basket fa-3x mb-3"></i>
                                    <p>Order is empty</p>
                                </div>
                            </div>

                            <div class="order-footer">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-weight-bold" id="subtotal">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-gray-600">Items</span>
                                    <span class="font-weight-bold" id="item-count">0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-gray-600">Service Charge (<?php echo $service_charge_percentage; ?>%)</span>
                                    <span class="font-weight-bold" id="service-charge">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 align-items-center">
                                    <span class="text-gray-600">Discount (<input type="number" id="discount-percent" class="form-control form-control-sm d-inline-block text-right" style="width: 60px; padding: 0 5px;" value="0" min="0" max="100" oninput="renderCart()" onfocus="setActiveInput(this.id)">%)</span>
                                    <span class="font-weight-bold" id="discount">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 align-items-center">
                                    <span class="text-gray-600">Advance</span>
                                    <input type="number" id="advance-amount" class="form-control form-control-sm d-inline-block text-right" style="width: 80px; padding: 0 5px;" value="0" min="0" oninput="renderCart()" onfocus="setActiveInput(this.id)">
                                </div>
                                <div class="d-flex justify-content-between mb-2 align-items-center">
                                    <span class="text-gray-600">Damage Claim</span>
                                    <input type="number" id="damage-claim" class="form-control form-control-sm d-inline-block text-right" style="width: 80px; padding: 0 5px;" value="0" min="0" oninput="renderCart()" onfocus="setActiveInput(this.id)">
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h4 font-weight-bold text-gray-800">TOTAL</span>
                                    <span class="total-display" id="total-amount">0.00</span>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6 pr-1">
                                        <button class="btn btn-warning btn-block shadow-sm font-weight-bold position-relative" onclick="viewPendingOrders()">
                                            <i class="fas fa-clock mr-1"></i> Pending KOT
                                            <span class="badge badge-danger position-absolute" id="badge-pending" style="top: -6px; right: -6px; font-size: 0.8rem; <?php echo $initial_pending > 0 ? '' : 'display:none;'; ?> border-radius: 50%; padding: 4px 6px;"><?php echo $initial_pending; ?></span>
                                        </button>
                                    </div>
                                    <div class="col-6 pl-1">
                                        <button class="btn btn-info btn-block shadow-sm font-weight-bold position-relative" onclick="viewOrderStatus()">
                                            <i class="fas fa-check-double mr-1"></i> Processing KOT
                                            <span class="badge badge-danger position-absolute" id="badge-processing" style="top: -6px; right: -6px; font-size: 0.8rem; <?php echo $initial_processing > 0 ? '' : 'display:none;'; ?> border-radius: 50%; padding: 4px 6px;"><?php echo $initial_processing; ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel (Menu + Keypad) -->
                        <div class="col-md-8 right-panel">
                            <!-- Search Bar -->
                            <div class="bg-white p-2 border-bottom shadow-sm">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-primary"></i></span></div>
                                    <input type="text" id="productSearch" class="form-control border-left-0 shadow-none" placeholder="Search products by name..." oninput="searchProducts()">
                                </div>
                            </div>
                            <!-- Parent Categories -->
                            <div class="parent-tabs">
                                <ul class="nav nav-pills" id="parent-tabs-list" role="tablist">
                                    <?php 
                                    if(isset($parent_categories) && $parent_categories) {
                                        $isFirstParent = true;
                                        foreach ($parent_categories as $pcat) {
                                            $pid = $pcat['ID'];
                                            $activeClass = $isFirstParent ? 'active' : ''; // Make the first parent active
                                            $ariaSelected = $isFirstParent ? 'true' : 'false'; // Make the first parent active
                                            $pname = isset($pcat['ParentCategoryName']) ? htmlspecialchars($pcat['ParentCategoryName']) : 'Parent Category';
                                            echo "<li class='nav-item'>
                                                    <a class='nav-link $activeClass' id='parent-$pid-tab' data-toggle='pill' href='javascript:void(0)' onclick=\"filterSubCategories($pid)\" role='tab' aria-controls='parent-$pid' aria-selected='$ariaSelected'>$pname</a>
                                                  </li>";
                                            $isFirstParent = false;
                                        }
                                    }
                                    // Add an "All Parent Categories" option
                                    echo "<li class='nav-item'>
                                            <a class='nav-link' id='parent-all-tab' data-toggle='pill' href='javascript:void(0)' onclick=\"filterSubCategories('all')\" role='tab' aria-controls='parent-all' aria-selected='false'>All Categories</a>
                                          </li>";
                                    ?>
                                </ul>
                            </div>

                            

                            <!-- Menu Categories (Top Tabs) -->
                            <div class="menu-tabs">
                                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                    <li class='nav-item sub-cat-item' data-parent-id='all' style="display: none;">
                                        <a class='nav-link active' id='pills-all-tab' data-toggle='pill' href='#pills-all' role='tab' aria-controls='pills-all' aria-selected='true'>All Items</a>
                                    </li>
                                    <?php 
                                    $first = false;
                                    if($cats) {
                                        foreach ($cats as $cat) {
                                            $active = ''; // No sub-category active by default, will be set by filterSubCategories
                                            $id = $cat['ID'];
                                            $parentId = isset($cat['ParentCategoryID']) ? $cat['ParentCategoryID'] : '';
                                            echo "<li class='nav-item sub-cat-item' data-parent-id='$parentId'>
                                                    <a class='nav-link $active' id='pills-$id-tab' data-toggle='pill' href='#pills-$id' role='tab' aria-controls='pills-$id' aria-selected='$first'>" . htmlspecialchars($cat['CategoryName']) . "</a>
                                                  </li>";
                                            $first = false;
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                            

                            <!-- Food Items Grid -->
                            <div class="food-grid-container">
                                <div class="tab-content" id="pills-tabContent">
                                    <?php 
                                    // This section will be dynamically updated by renderItems, so we can simplify it.
                                    if($cats) {
                                        foreach ($cats as $index => $cat) {
                                            $catId = $cat['ID'];
                                            $active = $first ? 'show active' : '';
                                            $colorClass = 'cat-color-' . ($index % 5); // Cycle through colors
                                            
                                            echo "<div class='tab-pane fade $active' id='pills-$catId' role='tabpanel' aria-labelledby='pills-$catId-tab'>";
                                            echo "<div class='row' id='products-for-cat-$catId'></div>"; // Products will be loaded here by JS
                                            echo "</div>";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- Bottom Section (Billing Controls) -->
                            <div class="billing-section">
                                <div class="keypad-grid">
                                    <button class="keypad-btn btn-func" style="grid-column: span 2;" onclick="openNewBill()">Open new bill</button>
                                    <button class="keypad-btn btn-func" style="grid-column: span 2;" onclick="printKOT()">KOT Print</button>
                                    <button class="keypad-btn btn-num" onclick="keypadInput(7)">7</button>
                                    <button class="keypad-btn btn-num" onclick="keypadInput(8)">8</button>
                                    <button class="keypad-btn btn-num" onclick="keypadInput(9)">9</button>
                                    <button class="keypad-btn btn-red btn-big" onclick="clearCart()">CLEAR</button>
                                    
                                    <button class="keypad-btn btn-num" onclick="keypadInput(4)">4</button>
                                    <button class="keypad-btn btn-num" onclick="keypadInput(5)">5</button>
                                    <button class="keypad-btn btn-num" onclick="keypadInput(6)">6</button>
                                    
                                    <button class="keypad-btn btn-num" onclick="keypadInput(1)">1</button>
                                    <button class="keypad-btn btn-num" onclick="keypadInput(2)">2</button>
                                    <button class="keypad-btn btn-num" onclick="keypadInput(3)">3</button>
                                    <button class="keypad-btn btn-green btn-big" onclick="closeBill()">Close the bill</button>
                                    
                                    <button class="keypad-btn btn-num" onclick="keypadInput(0)">0</button>
                                    <button class="keypad-btn btn-num" onclick="keypadInput('00')">00</button>
                                    <button class="keypad-btn btn-num" onclick="keypadInput('.')">.</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Main Content -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->
    <?php include_once('../include/script.php'); ?>

    <script>
        var productsData = <?php echo json_encode($products_data); ?>;
        var categoriesList = <?php echo json_encode($cats); ?>;
        var current_staff_id = <?php echo intval($_SESSION['uid']); ?>;
        var serviceChargePercentage = <?php echo $service_charge_percentage; ?>;
        // Basic Clock
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').innerText = now.toLocaleString();
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Cart Logic
        let cart = [];
        let activeInputId = null;

        function setActiveInput(id) {
            activeInputId = id;
        }
        
        function addToCart(id, name, price) {
            // Find an item with the same ID added by the *current cashier*.
            const existingItem = cart.find(item => item.id === id && item.staff_id === current_staff_id);
            if (existingItem) {
                existingItem.qty++;
            } else {
                // If no existing item from this cashier, add a new one.
                cart.push({ id, name, price, qty: 1, staff_id: current_staff_id });
            }
            
            // User-friendly Toast Feedback
            const Toast = Swal.mixin({
                toast: true, position: 'bottom-end', showConfirmButton: false, timer: 1000, timerProgressBar: true
            });
            Toast.fire({ icon: 'success', title: name + ' added' });

            renderCart();
        }

        function renderCart() {
            const orderList = document.getElementById('order-list');
            let subtotal = 0;
            let count = 0;

            if (cart.length === 0) {
                orderList.innerHTML = `
                    <div class="text-center mt-5 text-gray-400">
                        <i class="fas fa-shopping-basket fa-3x mb-3"></i>
                        <p>Order is empty</p>
                    </div>`;
            } else {
                let html = '';
                cart.forEach((item, index) => {
                    const total = item.price * item.qty;
                    subtotal += total;
                    count += item.qty;
                    // Only allow editing if item is new (undefined) or status is 0 (Pending)
                    const canEdit = (item.order_status === undefined || item.order_status === 0);
                    html += `
                        <div class="order-item d-flex justify-content-between align-items-center">
                            <div style="flex:1">
                                <div class="font-weight-bold text-gray-800">${item.name}</div>
                                <div class="d-flex align-items-center mt-1">
                                    <small class="text-muted mr-2">${parseFloat(item.price).toFixed(2)}</small>
                                    ${canEdit ? `
                                    <button class="btn btn-sm btn-light border py-0 px-2" onclick="updateItemQty(${index}, -1)">-</button>
                                    <input type="number" id="qty-${index}" class="form-control form-control-sm mx-2 text-center p-0" style="width: 45px; height: 25px;" value="${item.qty}" onchange="setQty(${index}, this.value)" onfocus="setActiveInput(this.id)">
                                    <button class="btn btn-sm btn-light border py-0 px-2" onclick="updateItemQty(${index}, 1)">+</button>
                                    <button class="btn btn-sm btn-danger py-0 px-2 ml-2" onclick="removeItem(${index})"><i class="fas fa-trash"></i></button>
                                    ` : `
                                    <span class="badge badge-info">Printed (Qty: ${item.qty})</span>
                                    `}
                                </div>
                            </div>
                            <div class="font-weight-bold text-gray-800">${total.toFixed(2)}</div>
                        </div>`;
                });
                orderList.innerHTML = html;
            }

            let serviceCharge = 0;
            if (currentOrderType === 'Dine-in') {
                serviceCharge = subtotal * (serviceChargePercentage / 100);
            }

            let discountPercent = parseFloat(document.getElementById('discount-percent').value) || 0;
            
            // Enforce range 0 - 100
            if (discountPercent > 100) {
                discountPercent = 100;
                document.getElementById('discount-percent').value = 100;
            } else if (discountPercent < 0) {
                discountPercent = 0;
                document.getElementById('discount-percent').value = 0;
            }

            let discountAmount = serviceCharge * (discountPercent / 100);
            let advanceAmount = parseFloat(document.getElementById('advance-amount').value) || 0;
            let damageClaim = parseFloat(document.getElementById('damage-claim').value) || 0;

            let total = subtotal + serviceCharge + damageClaim - discountAmount - advanceAmount;
            if (total < 0) total = 0;

            document.getElementById('subtotal').innerText = subtotal.toFixed(2);
            document.getElementById('item-count').innerText = count;
            document.getElementById('service-charge').innerText = serviceCharge.toFixed(2);
            document.getElementById('discount').innerText = discountAmount.toFixed(2);
            document.getElementById('total-amount').innerText = total.toFixed(2);
        }

        // Function to render products based on the active sub-category tab
        window.renderItems = function() {
            // Get the currently active sub-category tab
            const activeSubCategoryTab = $('#pills-tab .nav-link.active');
            if (activeSubCategoryTab.length === 0) return;

            const selectedSubId = activeSubCategoryTab.attr('id').replace('pills-', '').replace('-tab', '');
            
            // Clear current content in the active pane
            $(`#pills-${selectedSubId} .row`).empty();

            let productsToDisplay = [];
            
            if (productsData[selectedSubId]) {
                productsToDisplay = productsData[selectedSubId];
            }

            let html = '';
            if (productsToDisplay.length > 0) {
                productsToDisplay.forEach(function(product) {
                    const pName = product.ProductName;
                    const pPrice = parseFloat(product.Price).toFixed(2);
                    const pId = product.ID;
                    const pQty = parseInt(product.Quantity);
                    const isOutOfStock = (product.Type === 'Countable' && pQty <= 0);
                    const clickAction = isOutOfStock ? "" : `onclick="addToCart(${pId}, '${pName.replace(/'/g, "\\'")}', ${product.Price})"`;
                    const stockLabel = isOutOfStock ? "<br><small class='badge badge-light text-danger'>Out of Stock</small>" : "";
                    const opacity = isOutOfStock ? "style='opacity: 0.6; cursor: not-allowed;'" : "";
                    const colorClass = 'cat-color-' + (product.SubCategoryID % 5);

                    html += `
                        <div class='col-xl-3 col-md-4 col-6 mb-3'>
                            <div class='food-card ${colorClass}' ${clickAction} ${opacity}>
                                <span class='item-name'>${pName} ${stockLabel}</span>
                                <span class='item-price'>${pPrice}</span>
                            </div>
                        </div>`;
                });
            } else {
                html = '<div class="col-12 text-center text-gray-500 mt-4">No items in this category</div>';
            }

            $(`#pills-${selectedSubId} .row`).html(html);
        };

        function searchProducts() {
            let query = document.getElementById('productSearch').value.toLowerCase();
            let panes = document.querySelectorAll('.tab-pane');
            let cards = document.querySelectorAll('.food-card');
            let categoryTabs = document.querySelector('.menu-tabs');
            let parentTabs = document.querySelector('.parent-tabs');

            if (query.length > 0) {
                // When searching, hide category navigation to focus on results
                categoryTabs.style.display = 'none';
                parentTabs.style.display = 'none';
                
                panes.forEach(pane => pane.classList.add('show', 'active'));

                cards.forEach(card => {
                    let name = card.querySelector('.item-name').innerText.toLowerCase();
                    let container = card.closest('.col-xl-3');
                    container.style.display = name.includes(query) ? "" : "none";
                });
            } else {
                // Reset to standard tab-based view
                categoryTabs.style.display = '';
                parentTabs.style.display = '';
                
                const activeTabLink = document.querySelector('#pills-tab .nav-link.active');
                const activePaneId = activeTabLink ? activeTabLink.getAttribute('href').replace('#', '') : null;
                
                panes.forEach(pane => {
                    pane.classList.remove('show', 'active');
                    if (pane.id === activePaneId) pane.classList.add('show', 'active');
                });
                document.querySelectorAll('.food-grid-container .row > div').forEach(col => col.style.display = "");
            }
        }

        function filterSubCategories(parentId) {
            // Update active state of parent tabs
            document.querySelectorAll('#parent-tabs-list .nav-link').forEach(el => {
                el.classList.remove('active');
                el.setAttribute('aria-selected', 'false');
            });
            
            const activeTab = document.getElementById('parent-' + parentId + '-tab');
            
            if(activeTab) {
                activeTab.classList.add('active');
                activeTab.setAttribute('aria-selected', 'true');
            }

            // Filter items
            const items = document.querySelectorAll('.sub-cat-item');
            let firstVisible = null;
            let currentActiveIsVisible = false;

            items.forEach(item => {
                const itemParentId = item.getAttribute('data-parent-id');
                if (itemParentId == parentId) {
                    item.style.display = ''; //block
                    if (!firstVisible) firstVisible = item;
                    if (item.querySelector('.nav-link').classList.contains('active')) {
                        currentActiveIsVisible = true;
                    }
                } else {
                    item.style.display = 'none';
                }
            });

            // if current active tab is hidden, switch to first visibile
            if (!currentActiveIsVisible && firstVisible) {
                firstVisible.querySelector('.nav-link').click();
            }
        }

        function clearCart() {
            cart = [];
            document.getElementById('discount-percent').value = 0;
            document.getElementById('damage-claim').value = 0;
            renderCart();
        }

        function keypadInput(val) {
            if (!activeInputId) return;
            let input = document.getElementById(activeInputId);
            if (input) {
                let currentVal = input.value.toString();
                if (val === '.' && currentVal.includes('.')) return;
                
                if (currentVal === '0' && val !== '.') input.value = val;
                else input.value = currentVal + val;
                
                input.dispatchEvent(new Event('input'));
                input.dispatchEvent(new Event('change'));
            }
        }

        function updateItemQty(index, change) {
            if (cart[index]) {
                let newQty = cart[index].qty + change;
                if (newQty <= 0) {
                    if(confirm("Remove item from cart?")) {
                        cart.splice(index, 1);
                    }
                } else {
                    cart[index].qty = newQty;
                }
                renderCart();
            }
        }

        function setQty(index, val) {
            let newQty = parseInt(val);
            if (isNaN(newQty) || newQty <= 0) {
                if(confirm("Remove item from cart?")) {
                    cart.splice(index, 1);
                }
            } else {
                cart[index].qty = newQty;
            }
            renderCart();
        }

        function removeItem(index) {
            const item = cart[index];
            if (item.detail_id) {
                // Item exists in DB, delete from table via AJAX
                Swal.fire({
                    title: 'Remove Item?',
                    text: "This item will be deleted from the order record.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, remove it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('delete_order_detail.php', { detail_id: item.detail_id }, function(res) {
                            if (res.status === 'success') {
                                cart.splice(index, 1);
                                renderCart();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }, 'json');
                    }
                });
            } else {
                // New item not yet saved, just remove from array
                cart.splice(index, 1);
                renderCart();
            }
        }

        function setPaymentMethod(btn, method) {
            document.getElementById('payment-method').value = method;
            document.querySelectorAll('.payment-btn').forEach(b => {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-primary', 'active');
        }

        let selectedTableId = null;
        let currentOrderId = null;
        let currentOrderType = 'Dine-in';

        function selectTable(name, id, orderId = null) {
            document.getElementById('tableSelectDropdown').innerText = name;
            selectedTableId = id;
            
            $.ajax({
                url: 'get_order_details.php',
                type: 'GET',
                data: { table_id: id, order_id: orderId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        cart = response.items;
                        currentOrderId = response.order_id;
                        // Sync the order type UI with the loaded order
                        setOrderType(response.order_type, false); 
                        document.getElementById('advance-amount').value = response.advance || 0;
                        document.getElementById('damage-claim').value = response.damage_claim || 0;
                        renderCart();
                    } else {
                        cart = [];
                        currentOrderId = null;
                        renderCart();
                    }
                },
                error: function() {
                    console.error("Failed to fetch order details");
                }
            });
        }

        function setOrderType(type, clear = true) {
            currentOrderType = type;
            
            // Update Badge
            const badge = document.querySelector('.dine-in-badge');
            if(badge) badge.innerText = type === 'Dine-in' ? 'DINE IN' : 'TAKE AWAY';
            
            const btnDine = document.getElementById('btn-dine-in');
            const btnTake = document.getElementById('btn-take-away');
            
            if (type === 'Dine-in') {
                btnDine.classList.add('btn-danger', 'active');
                btnDine.classList.remove('btn-outline-secondary');
                btnTake.classList.remove('btn-danger', 'active');
                btnTake.classList.add('btn-outline-secondary');
                
                if(selectedTableId === null) document.getElementById('tableSelectDropdown').innerText = 'Table Select';
                if(clear) renderCart();
            } else {
                // Take Away Mode
                btnTake.classList.add('btn-danger', 'active');
                btnTake.classList.remove('btn-outline-secondary');
                btnDine.classList.remove('btn-danger', 'active');
                btnDine.classList.add('btn-outline-secondary');
                
                if (clear) {
                    clearCart();
                    selectedTableId = null;
                    currentOrderId = null;
                }
                document.getElementById('tableSelectDropdown').innerText = 'Take Away';
            }
        }

        var allTables = <?php echo json_encode($js_tables); ?>;
        function openNewBill() {
            var available = allTables.filter(t => t.Status == '0');
            var reserved = allTables.filter(t => t.Status == '1');
            
            var html = '<div class="text-left">';
            html += '<h6 class="font-weight-bold text-success">Available</h6><div class="d-flex flex-wrap mb-3">';
            if(available.length === 0) html += '<small class="text-muted w-100">No available tables</small>';
            available.forEach(t => {
                html += `<button class="btn btn-outline-success m-1" onclick="selectAndClose('${t.TableName}', ${t.ID})">${t.TableName}</button>`;
            });
            html += '</div>';
            
            html += '<h6 class="font-weight-bold text-warning">Reserved</h6><div class="d-flex flex-wrap">';
            if(reserved.length === 0) html += '<small class="text-muted w-100">No reserved tables</small>';
            reserved.forEach(t => {
                html += `<button class="btn btn-outline-warning m-1" onclick="selectAndClose('${t.TableName}', ${t.ID})">${t.TableName}</button>`;
            });
            html += '</div></div>';

            Swal.fire({
                title: '<strong>Select Table</strong>',
                html: html,
                showConfirmButton: false,
                showCloseButton: true,
                width: 600
            });
        }

        function selectAndClose(name, id) {
            selectTable(name, id);
            clearCart();
            Swal.close();
        }

        function viewOrderStatus() {
            $.ajax({
                url: 'get_order_status.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let html = '<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">';
                        html += '<table class="table table-bordered table-striped text-left text-sm">';
                        html += '<thead class="thead-light"><tr><th>KOT No.</th><th>Order #</th><th>Items (Qty)</th><th>Action</th></tr></thead><tbody>';
                        
                        if (response.data.length === 0) {
                            html += '<tr><td colspan="4" class="text-center text-muted py-4">No items are currently processing in the kitchen.</td></tr>';
                        } else {
                            let grouped = {};
                            response.data.forEach(item => {
                                let groupKey = item.OrderID + '-' + item.KOT;
                                if(!grouped[groupKey]) {
                                    grouped[groupKey] = { OrderID: item.OrderID, KOT: item.KOT, ids: [], itemsHTML: '' };
                                }
                                grouped[groupKey].ids.push(item.ID);
                                grouped[groupKey].itemsHTML += `<div><span class="badge badge-secondary mr-2">${item.Qty}x</span> ${item.ProductName}</div>`;
                            });

                            for (let groupKey in grouped) {
                                let order = grouped[groupKey];
                                let idsStr = order.ids.join(',');
                                html += `<tr id="status-row-${groupKey}">
                                    <td class="align-middle font-weight-bold text-gray-800">${order.KOT}</td>
                                    <td class="align-middle text-gray-600">${order.OrderID}</td>
                                    <td class="align-middle">${order.itemsHTML}</td>
                                    <td class="align-middle text-center">
                                        <button class="btn btn-sm btn-success shadow-sm" onclick="markOrderCompleted('${idsStr}', '${groupKey}')">
                                            <i class="fas fa-check-double"></i> Complete All
                                        </button>
                                    </td>
                                </tr>`;
                            }
                        }
                        html += '</tbody></table></div>';

                        Swal.fire({
                            title: 'Kitchen Processing Status',
                            html: html,
                            width: 800,
                            showCloseButton: true,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', 'Failed to fetch status: ' + response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Server error while fetching status.', 'error');
                }
            });
        }

        function markOrderCompleted(idsStr, groupKey) {
            $.ajax({
                url: 'update_order_status.php',
                type: 'POST',
                data: { ids: idsStr, status: 2 },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let row = document.getElementById('status-row-' + groupKey);
                        if (row) {
                            row.style.transition = "opacity 0.3s ease";
                            row.style.opacity = "0";
                            setTimeout(() => { row.remove(); }, 300);
                        }
                        
                        const Toast = Swal.mixin({
                            toast: true, position: 'top-end', showConfirmButton: false, timer: 1500
                        });
                        Toast.fire({ icon: 'success', title: 'Order marked as Completed!' });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Server error while updating status.', 'error');
                }
            });
        }

        function viewPendingOrders() {
            $.ajax({
                url: 'get_pending_orders.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let html = '<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">';
                        html += '<table class="table table-bordered table-striped text-left text-sm">';
                        html += '<thead class="thead-light"><tr><th>KOT No.</th><th>Order #</th><th>Items (Qty)</th><th>Action</th></tr></thead><tbody>';
                        
                        if (response.data.length === 0) {
                            html += '<tr><td colspan="4" class="text-center text-muted py-4">No pending orders.</td></tr>';
                        } else {
                            let grouped = {};
                            response.data.forEach(item => {
                                let groupKey = item.OrderID + '-' + item.KOT;
                                if(!grouped[groupKey]) {
                                    grouped[groupKey] = { OrderID: item.OrderID, KOT: item.KOT, ids: [], itemsHTML: '' };
                                }
                                grouped[groupKey].ids.push(item.ID);
                                grouped[groupKey].itemsHTML += `<div><span class="badge badge-secondary mr-2">${item.Qty}x</span> ${item.ProductName}</div>`;
                            });

                            for (let groupKey in grouped) {
                                let order = grouped[groupKey];
                                let idsStr = order.ids.join(',');
                                html += `<tr id="pending-row-${groupKey}">
                                    <td class="align-middle font-weight-bold text-gray-800">${order.KOT}</td>
                                    <td class="align-middle text-gray-600">${order.OrderID}</td>
                                    <td class="align-middle">${order.itemsHTML}</td>
                                    <td class="align-middle text-center">
                                        <button class="btn btn-sm btn-primary shadow-sm" onclick="markOrderProcessing('${idsStr}', '${groupKey}', '${order.KOT}', ${order.OrderID})">
                                            <i class="fas fa-fire"></i> Start All
                                        </button>
                                    </td>
                                </tr>`;
                            }
                        }
                        html += '</tbody></table></div>';

                        Swal.fire({
                            title: 'Pending Orders',
                            html: html,
                            width: 800,
                            showCloseButton: true,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', 'Failed to fetch pending orders: ' + response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Server error while fetching pending orders.', 'error');
                }
            });
        }

        function markOrderProcessing(idsStr, groupKey, kotNum, orderId) {
            $.ajax({
                url: 'update_order_status.php',
                type: 'POST',
                data: { ids: idsStr, status: 1 },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        let row = document.getElementById('pending-row-' + groupKey);
                        if (row) {
                            row.style.transition = "opacity 0.3s ease";
                            row.style.opacity = "0";
                            setTimeout(() => { row.remove(); }, 300);
                        }
                        
                        const Toast = Swal.mixin({
                            toast: true, position: 'top-end', showConfirmButton: false, timer: 1500
                        });
                        Toast.fire({ icon: 'success', title: 'Order moved to Processing!' });

                        // Print the KOT
                        window.open('../waitor/print_kot.php?order_id=' + orderId + '&ids=' + idsStr + '&kot_num=' + kotNum, 'KOT', 'width=400,height=600');
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Server error while updating status.', 'error');
                }
            });
        }

        function printKOT() {
            const newItems = cart.filter(item => item.detail_id === undefined || item.order_status === 0);

            if (currentOrderId) { // Bill is already open for a table
                if (newItems.length > 0) {
                    // Save only the new items to the existing order
                    $.ajax({
                        url: 'save_new_items_to_order.php', // Call the new backend endpoint
                        type: 'POST',
                        data: {
                            order_id: currentOrderId,
                            new_items: JSON.stringify(newItems) // Send only the new items
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                // Print KOT for the newly added items
                                window.open('../waitor/print_kot.php?order_id=' + currentOrderId + '&ids=' + response.detail_ids.join(',') + '&kot_num=' + response.kot_num, 'KOT', 'width=400,height=600');

                                // Reload the cart to update item statuses and detail_ids
                                selectTable(document.getElementById('tableSelectDropdown').innerText, selectedTableId);
                                Swal.fire('Success', 'New items added and KOT printed!', 'success');
                            } else {
                                Swal.fire('Error', 'Failed to add new items: ' + response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error', 'AJAX error saving new items: ' + error, 'error');
                        }
                    });
                } else {
                    // No new items, maybe reprint KOT for existing items?
                    Swal.fire({
                        title: 'No New Items',
                        text: 'There are no new items to add to the order. Do you want to reprint the KOT for all existing items?',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Reprint KOT',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Fetch all item IDs for the current order and print KOT
                            $.ajax({
                                url: 'get_order_ids.php', // This fetches ALL item IDs for an existing order
                                type: 'POST',
                                data: { order_id: currentOrderId },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status === 'success' && response.ids.length > 0) {
                                        window.open('../waitor/print_kot.php?order_id=' + currentOrderId + '&ids=' + response.ids.join(',') + '&kot_num=REPRINT', 'KOT', 'width=400,height=600');
                                    } else {
                                        Swal.fire("Info", "No items found for this order to reprint KOT.", "info");
                                    }
                                },
                                error: function() { Swal.fire("Error", "Failed to fetch order details for reprinting KOT.", "error"); }
                            });
                        }
                    });
                }
            } else if (cart.length > 0) { // Scenario 3: New Order (Take Away or New Dine-in)
                let orderData = {
                    items: cart,
                    total: parseFloat(document.getElementById('total-amount').innerText),
                    serviceCharge: parseFloat(document.getElementById('service-charge').innerText),
                    discount: parseFloat(document.getElementById('discount').innerText),
                    advance: parseFloat(document.getElementById('advance-amount').value) || 0,
                    damageClaim: parseFloat(document.getElementById('damage-claim').value) || 0,
                    tableId: selectedTableId || 0,
                    orderType: currentOrderType,
                    orderStatus: 'Pending' // Save as Pending
                };

                $.ajax({
                    url: 'save_pos_order.php',
                    type: 'POST',
                    data: { order_data: JSON.stringify(orderData) },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Prevent own order from triggering the New Order Alert
                            if (response.ids && response.ids.length > 0) {
                                let maxId = Math.max(...response.ids);
                                if (maxId > lastOrderDetailId) {
                                    lastOrderDetailId = maxId;
                                }
                            }

                            // Update active order ID so subsequent clicks work on this order
                            currentOrderId = response.order_id;
                            
                            var ids = response.ids.join(',');
                            window.open('../waitor/print_kot.php?order_id=' + response.order_id + '&ids=' + ids + '&kot_num=' + response.kot_num, 'KOT', 'width=400,height=600');
                            
                            const Toast = Swal.mixin({
                                toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
                            });
                            Toast.fire({ icon: 'success', title: 'KOT Printed' });
                            
                            // After printing KOT for a new order, reload the cart to reflect saved items
                            // This is important to update detail_id and order_status for newly added items
                            if (selectedTableId) {
                                selectTable(document.getElementById('tableSelectDropdown').innerText, selectedTableId);
                            } else {
                                // For take-away, clear the cart as it's a new order
                                clearCart();
                            }

                        } else {
                            Swal.fire("Error", response.message, "error");
                        }
                    },
                    error: function() { Swal.fire("Error", "Failed to save order", "error"); }
                });
            } else {
                Swal.fire("Error", "Cart is empty. Add items before printing KOT.", "error");
            }
        }

        function closeBill() {
            if (cart.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Cart',
                    text: 'Please add items to the cart before closing the bill.'
                });
                return;
            }
            
            if (selectedTableId === null && currentOrderType === 'Dine-in') {
                 Swal.fire({
                    icon: 'warning',
                    title: 'No Table Selected',
                    text: 'Please select a table to proceed.'
                });
                return;
            }

            Swal.fire({
                title: 'Close Bill?',
                text: "Confirm to close this bill.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1cc88a',
                cancelButtonColor: '#e74a3b',
                confirmButtonText: 'Yes, close it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let orderData = {
                        items: cart,
                        total: parseFloat(document.getElementById('total-amount').innerText),
                        serviceCharge: parseFloat(document.getElementById('service-charge').innerText),
                        discount: parseFloat(document.getElementById('discount').innerText),
                        advance: parseFloat(document.getElementById('advance-amount').value) || 0,
                        damageClaim: parseFloat(document.getElementById('damage-claim').value) || 0,
                        tableId: selectedTableId || 0,
                        orderId: currentOrderId || 0,
                        orderType: currentOrderType,
                        orderStatus: 'Paid'
                    };

                    $.ajax({
                        url: 'save_pos_order.php',
                        type: 'POST',
                        data: { order_data: JSON.stringify(orderData) },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                window.open('receipt.php?order_id=' + response.order_id, '_blank', 'width=400,height=600');
                                
                                // If Take Away, also print KOT
                                if (currentOrderType === 'Take-away') {
                                    var ids = response.ids.join(',');
                                    window.open('../waitor/print_kot.php?order_id=' + response.order_id + '&ids=' + ids + '&kot_num=' + response.kot_num, 'KOT', 'width=400,height=600');
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Bill Closed',
                                    text: 'Printing receipt...',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Server Error', 'error');
                        }
                    });
                }
            });
        }

        // --- Real-time Order Polling ---
        let lastOrderDetailId = <?php echo $initial_max_id; ?>;
        let isNotifying = false;
        let audioPlayer = null;

        // Poll for pending tables every 5 seconds to update the dropdown
        setInterval(function() {
            $.ajax({
                url: 'get_pending_tables.php',
                type: 'GET',
                success: function(data) {
                    $('#tableSelectMenu').html(data);
                }
            });
        }, 5000);

        // Tab show event listener to trigger product rendering
        $(document).on('shown.bs.tab', 'a[data-toggle="pill"]', function (e) {
            renderItems();
        });

        setInterval(function() {
            // Prevent multiple overlapping alerts or sounds
            if (isNotifying || Swal.isVisible()) return;

            $.ajax({
                url: 'check_new_orders.php',
                type: 'GET',
                data: { last_id: lastOrderDetailId },
                dataType: 'json',
                success: function(response) {
                    // Always update the badges first
                    if (response.pending_count !== undefined) {
                        let pendBadge = document.getElementById('badge-pending');
                        pendBadge.innerText = response.pending_count;
                        pendBadge.style.display = response.pending_count > 0 ? 'inline-block' : 'none';
                    }
                    if (response.processing_count !== undefined) {
                        let procBadge = document.getElementById('badge-processing');
                        procBadge.innerText = response.processing_count;
                        procBadge.style.display = response.processing_count > 0 ? 'inline-block' : 'none';
                    }

                    if (response.has_new) {
                        lastOrderDetailId = response.new_max_id;
                        isNotifying = true;
                        
                        // 1. Play the notification sound from branding in a loop
                        if (response.sound) {
                            audioPlayer = new Audio('../branding/uploads/' + response.sound);
                            audioPlayer.loop = true;
                            audioPlayer.play().catch(e => console.log("Interaction required for audio."));
                        }

                        Swal.fire({
                            title: 'New Order Alert!',
                            text: 'Order #' + response.order_id + ' has new items.',
                            icon: 'warning',
                            position: 'top-end',
                            showConfirmButton: true, 
                            confirmButtonText: 'Print KOT',
                            showCloseButton: true,
                            allowOutsideClick: false,
                            backdrop: false,
                            customClass: {
                                popup: 'animated tada'
                            }
                        }).then((result) => {
                            // 2. Stop and reset the sound when clicking Print KOT or closing
                            if (audioPlayer) {
                                audioPlayer.pause();
                                audioPlayer.currentTime = 0;
                            }
                            isNotifying = false;

                            if (result.isConfirmed) {
                                // 1. Update the status in the database to 1 (Processing)
                                $.ajax({
                                    url: 'update_order_status.php',
                                    type: 'POST',
                                    data: { ids: response.ids, status: 1 },
                                    dataType: 'json',
                                    success: function(updateRes) {
                                        // 2. Open the KOT Print Window
                                        window.open('../waitor/print_kot.php?order_id=' + response.order_id + '&ids=' + response.ids + '&kot_num=' + response.kot_num, 'KOT', 'width=400,height=600');
                                    },
                                    error: function() {
                                        console.error("Failed to update status to processing");
                                        // Fallback: still print the KOT even if the update fails
                                        window.open('../waitor/print_kot.php?order_id=' + response.order_id + '&ids=' + response.ids + '&kot_num=' + response.kot_num, 'KOT', 'width=400,height=600');
                                    }
                                });
                            }
                        });
                    }
                },
                error: function(err) {
                    console.error("Polling error: ", err);
                }
            });
        }, 5000);

        // Initialize the first parent category filter on load
        const firstParentTab = document.querySelector('#parent-tabs-list .nav-link');
        if (firstParentTab) {
            firstParentTab.click();
        }
    </script>
</body>

</html>