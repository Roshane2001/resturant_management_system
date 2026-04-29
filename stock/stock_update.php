<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    header('location:../auth/login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['stock_qty']);

    if (empty($product_id)) {
        echo "Product ID is missing.";
        exit;
    }

    // Get product name for the activity log
    $stmt_name = $con->prepare("SELECT ProductName FROM tblproducts WHERE ID = ?");
    $stmt_name->bind_param("i", $product_id);
    $stmt_name->execute();
    $res_name = $stmt_name->get_result();
    $product = $res_name->fetch_assoc();
    $product_name = $product['ProductName'] ?? 'Unknown Product';
    $stmt_name->close();

    // Update details in tblproducts
    $stmt_prod = $con->prepare("UPDATE tblproducts SET Quantity = Quantity + ? WHERE ID = ?");
    $stmt_prod->bind_param("ii", $quantity, $product_id);
    
    if ($stmt_prod->execute()) {
        $stmt_prod->close();

        // Log user activity for the stock update
        $user_id = $_SESSION['uid'];
        $activity_desc = "Added $quantity units to stock for product: $product_name";
        $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
        $log_stmt = mysqli_prepare($con, $log_sql);
        if ($log_stmt) {
            mysqli_stmt_bind_param($log_stmt, "is", $user_id, $activity_desc);
            mysqli_stmt_execute($log_stmt);
            mysqli_stmt_close($log_stmt);
        }

        echo 'yes';
    } else {
        echo "Error updating product quantity: " . $stmt_prod->error;
        $stmt_prod->close();
    }
    $con->close();
}
?>