<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    header('location:../auth/login.php');
    exit;
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Fetch table name before deletion for activity logging
    $stmt_name = $con->prepare("SELECT TableName FROM tbltables WHERE ID = ?");
    $stmt_name->bind_param("i", $id);
    $stmt_name->execute();
    $res_name = $stmt_name->get_result();
    $table_data = $res_name->fetch_assoc();
    $table_name = $table_data['TableName'] ?? 'Unknown';
    $stmt_name->close();

    $stmt = $con->prepare("DELETE FROM tbltables WHERE ID = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Log user activity
        $user_id = $_SESSION['uid'];
        $activity_desc = "Deleted table: Table " . $table_name;
        $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
        if ($log_stmt = $con->prepare($log_sql)) {
            $log_stmt->bind_param("is", $user_id, $activity_desc);
            $log_stmt->execute();
            $log_stmt->close();
        }
        echo 'success';
    } else {
        echo 'Error: ' . $stmt->error;
    }
    $stmt->close();
    $con->close();
}
?>