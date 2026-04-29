<?php
session_start();
include('../include/dbconnection.php');

// Redirect to login if session is not set
if (empty($_SESSION['uid'])) {
    header('location:../auth/login.php');
    exit;
}

// Set the default date to today or the user-selected date
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>RMS - Daily Stock Report</title>
    <?php include_once('../include/header.php'); ?>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include_once('../include/sidebar.php'); ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include_once('../include/top-nav.php'); ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-4 text-gray-800">Stock Usage Report</h1>
                        <a href="print_stock_report.php?filter_date=<?php echo $filter_date; ?>" id="printReportBtn" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-print fa-sm text-white-50"></i> Print Report
                        </a>
                    </div>

                    

                    <!-- Filter Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Filter Stock by Day</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="" class="form-inline mb-4">
                                <div class="form-group mr-3">
                                    <label for="filter_date" class="mr-2 font-weight-bold">Date:</label>
                                    <input type="date" class="form-control shadow-sm" id="filter_date" name="filter_date" value="<?php echo $filter_date; ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary shadow-sm">
                                    <i class="fas fa-filter fa-sm text-white-50"></i> Generate Report
                                </button>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="stockReportTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th class="text-center">Sold on This Day</th>
                                            <th class="text-center">Current Inventory</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query calculates sales for the specific day using a subquery on tblorder_details
                                        $sql = "SELECT 
                                                    p.ID, 
                                                    p.ProductName, 
                                                    p.Quantity, 
                                                    c.CategoryName,
                                                    (SELECT SUM(od.Qty) FROM tblorder_details od WHERE od.ProductID = p.ID AND od.OrderDate = ?) as SoldQty
                                                FROM tblproducts p
                                                LEFT JOIN tblcategory c ON p.SubCategoryID = c.ID
                                                WHERE p.Type = 'Countable'
                                                ORDER BY SoldQty DESC, p.ProductName ASC";
                                        
                                        $stmt = $con->prepare($sql);
                                        $stmt->bind_param("s", $filter_date);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $cnt = 1;

                                        while($row = $result->fetch_assoc()) {
                                            $sold = $row['SoldQty'] ?: 0;
                                        ?>
                                        <tr>
                                            <td><?php echo $cnt++; ?></td>
                                            <td class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['ProductName']); ?></td>
                                            <td><?php echo htmlspecialchars($row['CategoryName'] ?? 'N/A'); ?></td>
                                            <td class="text-center <?php echo $sold > 0 ? 'text-danger font-weight-bold' : 'text-gray-400'; ?>">
                                                <?php echo $sold; ?>
                                            </td>
                                            <td class="text-center"><?php echo $row['Quantity']; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include_once('../include/footer.php'); ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <?php include_once('../include/script.php'); ?>
    <!-- Page level plugins -->
    <script src="/resturant-management-system/vendor/datatables/jquery.dataTables.js"></script>
    <script src="/resturant-management-system/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
    $(document).ready(function() {
        var table = $('#stockReportTable').DataTable({
            "order": [[ 3, "desc" ]] // Default sort by sales
        });

        // Update print link when date changes
        function updatePrintLink() {
            var date = $('#filter_date').val();
            var url = 'print_stock_report.php?filter_date=' + encodeURIComponent(date);
            $('#printReportBtn').attr('href', url);
        }
        $('#filter_date').on('change', updatePrintLink);
    });
    </script>
</body>

</html>