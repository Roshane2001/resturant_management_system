<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    header('location:../auth/login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];

        // Fetch name and role for activity log before deletion
        $stmt_name = $con->prepare("SELECT StaffName, StaffRole FROM tblstaff WHERE ID = ?");
        $stmt_name->bind_param("i", $id);
        $stmt_name->execute();
        $res_name = $stmt_name->get_result();
        $staff = $res_name->fetch_assoc();
        $staff_name = $staff['StaffName'] ?? 'Unknown';
        $staff_role = $staff['StaffRole'] ?? 'Unknown';
        $stmt_name->close();

        // Using prepared statements to prevent SQL injection
        $stmt = $con->prepare("DELETE FROM tblstaff WHERE ID = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Log user activity
            $user_id = $_SESSION['uid'];
            $activity_desc = "Deleted staff member: " . $staff_name . " (Role: " . $staff_role . ")";
            $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
            if ($log_stmt = $con->prepare($log_sql)) {
                $log_stmt->bind_param("is", $user_id, $activity_desc);
                $log_stmt->execute();
                $log_stmt->close();
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