<?php
session_start();
include('../../include/dbconnection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Fetch name before deleting
    $stmt_name = $con->prepare("SELECT ProductName FROM tblproducts WHERE ID = ?");
    $stmt_name->bind_param("i", $id);
    $stmt_name->execute();
    $res = $stmt_name->get_result()->fetch_assoc();
    $product_name = $res['ProductName'] ?? 'Unknown';
    $stmt_name->close();

    $stmt = $con->prepare("DELETE FROM tblproducts WHERE ID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Log activity
        $user_id = $_SESSION['uid'];
        $activity_desc = "Deleted product: " . $product_name;
        $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
        if ($log_stmt = mysqli_prepare($con, $log_sql)) {
            mysqli_stmt_bind_param($log_stmt, "is", $user_id, $activity_desc);
            mysqli_stmt_execute($log_stmt);
            mysqli_stmt_close($log_stmt);
        }
        echo 'success';
    } else {
        echo 'Error deleting product.';
    }
    $con->close();
}
?>