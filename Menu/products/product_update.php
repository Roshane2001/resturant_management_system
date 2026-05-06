<?php
session_start();
include('../../include/dbconnection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id']);
    $parent_id = $_POST['parent_category'];
    $sub_id = $_POST['sub_category'];
    $product_name = trim($_POST['product_name']);
    $price = $_POST['price'];
    $type = $_POST['type'];
    $unit = $_POST['unit'] ?? '';

    // Fetch current details for activity logging
    $stmt_old = $con->prepare("SELECT * FROM tblproducts WHERE ID = ?");
    $stmt_old->bind_param("i", $product_id);
    $stmt_old->execute();
    $old_data = $stmt_old->get_result()->fetch_assoc();
    $old_name = $old_data['ProductName'] ?? 'Unknown';
    $stmt_old->close();

    // Duplicate check...
    $check_stmt = $con->prepare("SELECT ID FROM tblproducts WHERE ProductName = ? AND ID != ?");
    $check_stmt->bind_param("si", $product_name, $product_id);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        echo "Product name already exists.";
        exit;
    }

    // Update query
    $stmt = $con->prepare("UPDATE tblproducts SET ParentCategoryID=?, SubCategoryID=?, ProductName=?, Price=?, Type=?, Unit=? WHERE ID=?");
    $stmt->bind_param("iisdssi", $parent_id, $sub_id, $product_name, $price, $type, $unit, $product_id);

    if ($stmt->execute()) {
        // Detect specific changes for the log
        $changes = [];
        if ($old_data['ProductName'] != $product_name) $changes[] = "Name";
        if (floatval($old_data['Price']) != floatval($price)) $changes[] = "Price";
        if ($old_data['SubCategoryID'] != $sub_id) $changes[] = "Category";
        if ($old_data['Type'] != $type) $changes[] = "Type";
        if ($old_data['Unit'] != $unit) $changes[] = "Unit";
        
        $change_str = !empty($changes) ? " (" . implode(', ', $changes) . ")" : " (No significant changes)";
        $activity_desc = "Updated product: " . $old_name . $change_str;

        $user_id = $_SESSION['uid'];
        $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
        if ($log_stmt = mysqli_prepare($con, $log_sql)) {
            mysqli_stmt_bind_param($log_stmt, "is", $user_id, $activity_desc);
            mysqli_stmt_execute($log_stmt);
            mysqli_stmt_close($log_stmt);
        }
        echo 'yes';
    } else {
        echo 'Update failed.';
    }
    $con->close();
}
?>