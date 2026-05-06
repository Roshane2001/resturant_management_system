<?php
session_start();
include('../../include/dbconnection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    $parent_category_id = $_POST['parent_category'];

    // Basic validation
    if (empty($category_id) || empty($category_name) || empty($parent_category_id)) {
        echo "Parent Category and Sub Category Name are required.";
        exit;
    }

    // Fetch current details for activity logging
    $stmt_old = $con->prepare("SELECT CategoryName, ParentCategoryID FROM tblcategory WHERE ID = ?");
    $stmt_old->bind_param("i", $category_id);
    $stmt_old->execute();
    $res_old = $stmt_old->get_result();
    $old_data = $res_old->fetch_assoc();
    $old_name = $old_data['CategoryName'] ?? 'Unknown';
    $stmt_old->close();

    // Check for duplicate category name, excluding the current category
    $check_stmt = $con->prepare("SELECT ID FROM tblcategory WHERE CategoryName = ? AND ParentCategoryID = ? AND ID != ?");
    $check_stmt->bind_param("sii", $category_name, $parent_category_id, $category_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        echo "Sub Category name already exists under this Parent Category. Please choose a different one.";
        $check_stmt->close();
        $con->close();
        exit;
    }
    $check_stmt->close();

    // Prepare the update query
    $stmt = $con->prepare("UPDATE tblcategory SET CategoryName=?, ParentCategoryID=? WHERE ID=?");
    $stmt->bind_param("sii", $category_name, $parent_category_id, $category_id);

    if ($stmt->execute()) {
        // Log user activity
        $user_id = $_SESSION['uid'];
        $changes = [];
        if ($old_data['CategoryName'] != $category_name) $changes[] = "Name";
        if ($old_data['ParentCategoryID'] != $parent_category_id) $changes[] = "Parent Category";
        
        $change_str = !empty($changes) ? " (" . implode(', ', $changes) . ")" : " (No changes)";
        $activity_desc = "Updated sub-category: " . $old_name . $change_str;
        
        $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
        if ($log_stmt = $con->prepare($log_sql)) {
            $log_stmt->bind_param("is", $user_id, $activity_desc);
            $log_stmt->execute();
            $log_stmt->close();
        }

        echo 'yes';
    } else {
        echo 'Something went wrong. Please try again. Error: ' . htmlspecialchars($stmt->error);
    }
    $stmt->close();
    $con->close();
} else {
    echo "Invalid request method.";
}
?>