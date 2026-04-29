<?php
session_start();
include('../../include/dbconnection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];

        // Fetch name for logging
        $stmt_name = $con->prepare("SELECT CategoryName FROM tblcategory WHERE ID = ?");
        $stmt_name->bind_param("i", $id);
        $stmt_name->execute();
        $res_name = $stmt_name->get_result();
        $category = $res_name->fetch_assoc();
        $category_name = $category['CategoryName'] ?? 'Unknown';
        $stmt_name->close();

        // Using prepared statements to prevent SQL injection
        $stmt = $con->prepare("DELETE FROM tblcategory WHERE ID = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Log user activity
            $user_id = $_SESSION['uid'];
            $activity_desc = "Deleted sub-category: " . $category_name;
            $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
            $log_stmt = mysqli_prepare($con, $log_sql);
            if ($log_stmt) {
                mysqli_stmt_bind_param($log_stmt, "is", $user_id, $activity_desc);
                mysqli_stmt_execute($log_stmt);
                mysqli_stmt_close($log_stmt);
            }

            echo 'success';
        } else {
            echo 'Error: ' . htmlspecialchars($stmt->error);
        }
        $stmt->close();
        $con->close();
    } else {
        echo 'Invalid ID provided.';
    }
} else {
    echo 'Invalid request method.';
}
?>