<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    header('location:../auth/login.php');
    exit;
}

$selected_year = isset($_GET['year']) ? $_GET['year'] : 'all';
$selected_month = isset($_GET['month']) ? $_GET['month'] : 'all';

$where = "WHERE Status = 'Paid'";
if ($selected_year !== 'all') {
    $where .= " AND YEAR(OrderDate) = " . intval($selected_year);
}
if ($selected_month !== 'all') {
    $where .= " AND MONTH(OrderDate) = " . intval($selected_month);
}

// Fetch branding for the header
$branding_sql = "SELECT company_name FROM tblbranding LIMIT 1";
$branding_query = mysqli_query($con, $branding_sql);
$branding = mysqli_fetch_assoc($branding_query);
$company_name = $branding['company_name'] ?? 'Restaurant Management System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Monthly Summary - <?php echo htmlspecialchars($company_name); ?></title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header h2 { margin: 5px 0; font-size: 18px; color: #666; }
        .info { margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .total-section { text-align: right; font-size: 16px; font-weight: bold; margin-top: 20px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1><?php echo htmlspecialchars($company_name); ?></h1>
        <h2>Monthly Summary Report</h2>
    </div>

    <div class="info">
        <strong>Filter:</strong> 
        Year: <?php echo $selected_year === 'all' ? 'All' : htmlspecialchars($selected_year); ?>, 
        Month: <?php 
            $month_names = [
                "01"=>"January", "02"=>"February", "03"=>"March", "04"=>"April", 
                "05"=>"May", "06"=>"June", "07"=>"July", "08"=>"August", 
                "09"=>"September", "10"=>"October", "11"=>"November", "12"=>"December"
            ];
            echo $selected_month === 'all' ? 'All' : ($month_names[$selected_month] ?? $selected_month); 
        ?><br>
        <strong>Generated On:</strong> <?php echo date('Y-m-d H:i:s'); ?>
    </div>

    <table>
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
            // Daily aggregation query mirroring monthly_report.php
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
            $totalServiceCharge = 0;
            $totalDiscount = 0;
            $totalAdvance = 0;
            $totalDamageClaim = 0;
            $totalPrice = 0;
            
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
                    <td style="font-weight: bold;"><?php echo number_format($row['DayTotalAmount'], 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f4f4f4;">
                <td colspan="3" style="text-align: right;">Grand Totals:</td>
                <td><?php echo number_format($totalServiceCharge, 2); ?></td>
                <td><?php echo number_format($totalDiscount, 2); ?></td>
                <td><?php echo number_format($totalAdvance, 2); ?></td>
                <td><?php echo number_format($totalDamageClaim, 2); ?></td>
                <td><?php echo number_format($totalPrice, 2); ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.close()">Close Window</button>
    </div>

</body>
</html>