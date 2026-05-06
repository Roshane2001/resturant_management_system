<?php
session_start();
include('../include/dbconnection.php');

// Log logout activity before destroying session
if (!empty($_SESSION['uid'])) {
    $user_id = $_SESSION['uid'];
    $activity_desc = "Logged out of the system";
    $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
    if ($log_stmt = $con->prepare($log_sql)) {
        $log_stmt->bind_param("is", $user_id, $activity_desc);
        $log_stmt->execute();
        $log_stmt->close();
    }
}

session_unset();
session_destroy();
header('location:login.php');
?>