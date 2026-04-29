<?php
session_start();
include('../../include/dbconnection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_id = $_POST['parent_category'];
    $category_name = $_POST['category_name'];

    // Basic validation
    if (empty($category_name) || empty($parent_id)) {
        echo "Category name and Parent Category are required.";
        exit;
    }

    // Check for duplicate category name
    $check_stmt = $con->prepare("SELECT ID FROM tblcategory WHERE CategoryName = ?");
    $check_stmt->bind_param("s", $category_name);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        echo "Category name already exists. Please choose a different one.";
        $check_stmt->close();
        $con->close();
        exit;
    }
    $check_stmt->close();

    // Using prepared statements to prevent SQL injection
    $stmt = $con->prepare("INSERT INTO tblcategory (ParentCategoryID, CategoryName) VALUES (?, ?)");
    $stmt->bind_param("is", $parent_id, $category_name);

    if ($stmt->execute()) {
        // Log user activity
        $user_id = $_SESSION['uid'];
        $activity_desc = "Added new sub-category: " . $category_name;
        $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
        
        if ($log_stmt = mysqli_prepare($con, $log_sql)) {
            mysqli_stmt_bind_param($log_stmt, "is", $user_id, $activity_desc); // 'i' for integer, 's' for string
            if (!mysqli_stmt_execute($log_stmt)) {
                // Log the error if execution fails
                error_log("Error inserting activity log: " . mysqli_stmt_error($log_stmt));
                // Optionally, you could echo an error here for debugging, but usually not in production
                // echo "Error logging activity: " . htmlspecialchars(mysqli_stmt_error($log_stmt));
            }
            mysqli_stmt_close($log_stmt);
        } else {
            // Log the error if preparation fails
            error_log("Error preparing activity log statement: " . mysqli_error($con));
            // echo "Error preparing log statement: " . htmlspecialchars(mysqli_error($con));
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