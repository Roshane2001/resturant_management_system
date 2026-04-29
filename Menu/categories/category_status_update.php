<?php
session_start();
include('../../include/dbconnection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id']) && isset($_POST['status'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];

        // Validate status
        if ($status != 0 && $status != 1) {
            echo 'Invalid status value.';
            exit;
        }

        // Fetch name for logging
        $stmt_name = $con->prepare("SELECT CategoryName FROM tblcategory WHERE ID = ?");
        $stmt_name->bind_param("i", $id);
        $stmt_name->execute();
        $res_name = $stmt_name->get_result();
        $category = $res_name->fetch_assoc();
        $category_name = $category['CategoryName'] ?? 'Unknown';
        $stmt_name->close();

        // Using prepared statements to prevent SQL injection
        $stmt = $con->prepare("UPDATE tblcategory SET Status = ? WHERE ID = ?");
        $stmt->bind_param("ii", $status, $id);

        if ($stmt->execute()) {
            // Log user activity
            $user_id = $_SESSION['uid'];
            $status_txt = ($status == 1) ? "Activated" : "Deactivated";
            $activity_desc = "$status_txt sub-category: " . $category_name;
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
        echo 'Invalid parameters provided.';
    }
} else {
    echo 'Invalid request method.';
}
?>