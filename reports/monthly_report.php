<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    header('location:../auth/login.php');
    exit;
}

$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');

$where = "WHERE Status = 'Paid'";
if ($selected_year !== 'all') {
    $where .= " AND YEAR(OrderDate) = " . intval($selected_year);
}
if ($selected_month !== 'all') {
    $where .= " AND MONTH(OrderDate) = " . intval($selected_month);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    
    <title>RMS - Monthly Report</title>
    <?php include('../include/header.php'); ?>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include('../include/sidebar.php'); ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include('../include/top-nav.php'); ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Monthly Report</h1>
                        <a href="print_monthly_report.php?year=<?php echo $selected_year; ?>&month=<?php echo $selected_month; ?>" id="printReportBtn" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-print fa-sm text-white-50"></i> Print Report
                        </a>
                    </div>

                    <div class="card shadow mb-4">                       
                        <div class="card-body">
                            <!-- Filter Form -->
                            <form method="GET" class="form-inline mb-4">
                                <div class="form-group mr-3">
                                    <label class="mr-2">Year:</label>
                                    <select name="year" class="form-control form-control-sm">
                                        <option value="all" <?php echo $selected_year === 'all' ? 'selected' : ''; ?>>All Years</option>
                                        <?php 
                                        $start_year = date('Y');
                                        for($i = $start_year; $i >= $start_year - 5; $i--): ?>
                                            <option value="<?php echo $i; ?>" <?php echo $selected_year == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <label class="mr-2">Month:</label>
                                    <select name="month" class="form-control form-control-sm">
                                        <option value="all" <?php echo $selected_month === 'all' ? 'selected' : ''; ?>>All Months</option>
                                        <?php
                                        $months = [
                                            "01"=>"January", "02"=>"February", "03"=>"March", "04"=>"April", 
                                            "05"=>"May", "06"=>"June", "07"=>"July", "08"=>"August", 
                                            "09"=>"September", "10"=>"October", "11"=>"November", "12"=>"December"
                                        ];
                                        foreach($months as $num => $name): ?>
                                            <option value="<?php echo $num; ?>" <?php echo $selected_month == $num ? 'selected' : ''; ?>><?php echo $name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Total Orders</th>
                                            <th>Dine-in / Take-away</th>
                                            <th>Service Chg</th>
                                            <th>Discount</th>
                                            <th>Advance</th>
                                            <th>Damage Claim</th>
                                            <th>Total Income</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $totalServiceCharge = 0;
                                        $totalDiscount = 0;
                                        $totalAdvance = 0;
                                        $totalDamageClaim = 0;
                                        $totalPrice = 0;

                                        // Aggregating orders by Date
                                        $query = "SELECT 
                                                    DATE(OrderDate) as ReportDate,
                                                    COUNT(ID) as TotalOrders,
                                                    SUM(CASE WHEN OrderType LIKE '%Dine%' THEN 1 ELSE 0 END) as DineInCount,
                                                    SUM(CASE WHEN OrderType LIKE '%Take%' THEN 1 ELSE 0 END) as TakeAwayCount,
                                                    SUM(ServiceCharge) as DayServiceCharge,
                                                    SUM(Discount) as DayDiscount,
                                                    SUM(Advance) as DayAdvance,
                                                    SUM(DamageClaim) as DayDamageClaim,
                                                    SUM(TotalAmount) as DayTotalAmount
                                                  FROM tblorder 
                                                  $where
                                                  GROUP BY DATE(OrderDate)
                                                  ORDER BY ReportDate DESC";
                                        $ret = mysqli_query($con, $query);
                                        while ($row = mysqli_fetch_array($ret)) {
                                            $totalServiceCharge += $row['DayServiceCharge'];
                                            $totalDiscount += $row['DayDiscount'];
                                            $totalAdvance += $row['DayAdvance'];
                                            $totalDamageClaim += $row['DayDamageClaim'];
                                            $totalPrice += $row['DayTotalAmount'];
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['ReportDate']); ?></td>
                                                <td><?php echo $row['TotalOrders']; ?></td>
                                                <td><?php echo $row['DineInCount'] . ' / ' . $row['TakeAwayCount']; ?></td>
                                                <td><?php echo number_format($row['DayServiceCharge'], 2); ?></td>
                                                <td><?php echo number_format($row['DayDiscount'], 2); ?></td>
                                                <td><?php echo number_format($row['DayAdvance'], 2); ?></td>
                                                <td><?php echo number_format($row['DayDamageClaim'], 2); ?></td>
                                                <td class="font-weight-bold"><?php echo number_format($row['DayTotalAmount'], 2); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold bg-light">
                                            <td colspan="3" class="text-right">Totals:</td>
                                            <td id="footer-sc"><?php echo number_format($totalServiceCharge, 2); ?></td>
                                            <td id="footer-discount"><?php echo number_format($totalDiscount, 2); ?></td>
                                            <td id="footer-advance"><?php echo number_format($totalAdvance, 2); ?></td>
                                            <td id="footer-dc"><?php echo number_format($totalDamageClaim, 2); ?></td>
                                            <td id="footer-total"><?php echo number_format($totalPrice, 2); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include('../include/footer.php'); ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <?php include('../include/script.php'); ?>

    <!-- Page level plugins -->
    <script src="/resturant-management-system/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="/resturant-management-system/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                "order": [[0, "desc"]], // Sort by the first column (Order No) descending
                "drawCallback": function(settings) {
                    var api = this.api();
                    
                    // Helper function to parse numeric values from strings (handling commas and symbols)
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    // Calculate totals for filtered rows
                    var columnsToTotal = [3, 4, 5, 6, 7];
                    var footerIds = ['footer-sc', 'footer-discount', 'footer-advance', 'footer-dc', 'footer-total'];

                    columnsToTotal.forEach(function(colIdx, i) {
                        var total = api.column(colIdx, { filter: 'applied' }).data().reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                        $('#' + footerIds[i]).html(total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    });
                },
                "initComplete": function() {
                    // Filters removed as per request
                }
            });
        });
    </script>

</body>

</html>