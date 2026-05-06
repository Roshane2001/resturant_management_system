<?php
session_start();
include('../../include/dbconnection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_id = $_POST['parent_category'];
    $sub_id = $_POST['sub_category'];
    $product_name = trim($_POST['product_name']);
    $price = $_POST['price'];
    $type = $_POST['type'];
    $unit = $_POST['unit'] ?? '';

    if (empty($product_name) || empty($sub_id)) {
        echo "Product Name and Sub Category are required.";
        exit;
    }

    // Check for duplicate product name
    $check_stmt = $con->prepare("SELECT ID FROM tblproducts WHERE ProductName = ?");
    $check_stmt->bind_param("s", $product_name);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        echo "Product name already exists. Please choose a different one.";
        $check_stmt->close();
        $con->close();
        exit;
    }
    $check_stmt->close();

    // Insert product
    $stmt = $con->prepare("INSERT INTO tblproducts (ParentCategoryID, SubCategoryID, ProductName, Price, Type, Unit) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisdss", $parent_id, $sub_id, $product_name, $price, $type, $unit);

    if ($stmt->execute()) {
        // Log user activity
        $user_id = $_SESSION['uid'];
        $activity_desc = "Added new product: $product_name (Price: $price, Type: $type)";
        $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
        
        if ($log_stmt = mysqli_prepare($con, $log_sql)) {
            mysqli_stmt_bind_param($log_stmt, "is", $user_id, $activity_desc);
            if (!mysqli_stmt_execute($log_stmt)) {
                error_log("Error inserting activity log: " . mysqli_stmt_error($log_stmt));
            }
            mysqli_stmt_close($log_stmt);
        } else {
            error_log("Error preparing activity log statement: " . mysqli_error($con));
        }

        echo 'yes';
    } else {
        echo 'Something went wrong. Error: ' . htmlspecialchars($stmt->error);
    }
    $stmt->close();
    $con->close();
} else {
    echo "Invalid request method.";
}
?>