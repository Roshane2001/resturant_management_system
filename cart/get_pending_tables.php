<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    exit;
}

// Query to fetch both Dine-in (with table names) and Take-away orders
$sql = "SELECT t.ID as TableID, t.TableName, o.ID as OrderID, o.OrderType 
        FROM tblorder o 
        LEFT JOIN tbltables t ON o.TableID = t.ID 
        WHERE o.Status = 'Pending' 
        ORDER BY o.ID DESC";

$tbl_query = mysqli_query($con, $sql);

if (mysqli_num_rows($tbl_query) > 0) {
    while ($tbl_row = mysqli_fetch_assoc($tbl_query)) {
        $displayName = ($tbl_row['OrderType'] === 'Take-away') ? "Take Away #" . $tbl_row['OrderID'] : $tbl_row['TableName'];
        $tableNameJs = addslashes($displayName);
        $tableId = $tbl_row['TableID'] ? $tbl_row['TableID'] : 0;
        echo '<a class="dropdown-item" href="javascript:void(0)" onclick="selectTable(\'' . $tableNameJs . '\', ' . $tableId . ', ' . $tbl_row['OrderID'] . ')">' . htmlspecialchars($displayName) . ' - Bill #' . $tbl_row['OrderID'] . '</a>';
    }
} else {
    echo '<span class="dropdown-item text-muted">No pending tables</span>';
}
?>