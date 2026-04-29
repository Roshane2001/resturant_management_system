<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    header('location:../auth/login.php');
    exit;
}

$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : date('Y-m-d');

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
    <title>Print Stock Report - <?php echo htmlspecialchars($company_name); ?></title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header h2 { margin: 5px 0; font-size: 18px; color: #666; }
        .info { margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f4f4f4; font-weight: bold; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1><?php echo htmlspecialchars($company_name); ?></h1>
        <h2>Daily Stock Usage Report</h2>
    </div>

    <div class="info">
        <strong>Report Date:</strong> <?php echo htmlspecialchars($filter_date); ?>
        <br>
        <strong>Generated On:</strong> <?php echo date('Y-m-d H:i:s'); ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Category</th>
                <th style="text-align: center;">Sold Quantity</th>
                <th style="text-align: center;">Current Inventory</th>
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
                <td style="font-weight: bold;"><?php echo htmlspecialchars($row['ProductName']); ?></td>
                <td><?php echo htmlspecialchars($row['CategoryName'] ?? 'N/A'); ?></td>
                <td style="text-align: center;"><?php echo $sold; ?></td>
                <td style="text-align: center;"><?php echo $row['Quantity']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.close()">Close Window</button>
    </div>

</body>
</html>
<?php mysqli_close($con); ?>