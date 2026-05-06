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

    // Get current details for the activity log
    $stmt_name = $con->prepare("SELECT ProductName, Quantity FROM tblproducts WHERE ID = ?");
    $stmt_name->bind_param("i", $product_id);
    $stmt_name->execute();
    $res_name = $stmt_name->get_result();
    $product = $res_name->fetch_assoc();
    $product_name = $product['ProductName'] ?? 'Unknown Product';
    $old_qty = $product['Quantity'] ?? 0;
    $stmt_name->close();

    // Update details in tblproducts
    $stmt_prod = $con->prepare("UPDATE tblproducts SET Quantity = Quantity + ? WHERE ID = ?");
    $stmt_prod->bind_param("ii", $quantity, $product_id);
    
    if ($stmt_prod->execute()) {
        $stmt_prod->close();

        $new_qty = $old_qty + $quantity;
        // Log user activity for the stock update
        $user_id = $_SESSION['uid'];
        $activity_desc = "Stock Update: Added $quantity units to $product_name. (Total: $new_qty)";
        $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
        if ($log_stmt = $con->prepare($log_sql)) {
            $log_stmt->bind_param("is", $user_id, $activity_desc);
            $log_stmt->execute();
            $log_stmt->close();
        }

        echo 'yes';
    } else {
        echo "Error updating product quantity: " . $stmt_prod->error;
        $stmt_prod->close();
    }
    $con->close();
}
?>