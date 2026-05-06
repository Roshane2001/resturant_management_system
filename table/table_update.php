<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    header('location:../auth/login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table_id = $_POST['table_id'];
    $table_name = $_POST['table_name'];
    $table_chairs = $_POST['table_chairs'];
    $table_status = $_POST['table_status'];

    // Basic validation
    if (empty($table_id) || empty($table_name) || empty($table_chairs) || $table_status === null) {
        echo "All fields are required.";
        exit;
    }

    // Check for duplicate table name, excluding current table
    $check_stmt = $con->prepare("SELECT ID FROM tbltables WHERE TableName = ? AND ID != ?");
    $check_stmt->bind_param("si", $table_name, $table_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        echo "Table No already exists. Please choose a different one.";
        $check_stmt->close();
        $con->close();
        exit;
    }
    $check_stmt->close();

    // Fetch current details for activity logging
    $stmt_old = $con->prepare("SELECT TableName, ChairCount, Status FROM tbltables WHERE ID = ?");
    $stmt_old->bind_param("i", $table_id);
    $stmt_old->execute();
    $res_old = $stmt_old->get_result();
    $old_data = $res_old->fetch_assoc();
    $old_name = $old_data['TableName'] ?? 'Unknown';
    $stmt_old->close();

    // Status labels for descriptive logging
    $status_labels = [
        '0' => 'Available',
        '1' => 'Reserved',
        '2' => 'Seated'
    ];

    // Track specific changes for logging
    $changes = [];
    if (($old_data['TableName'] ?? '') != $table_name) $changes[] = "Table No to '$table_name'";
    if (($old_data['ChairCount'] ?? 0) != $table_chairs) $changes[] = "Chair Count to $table_chairs";
    if (($old_data['Status'] ?? '') != $table_status) {
        $old_status_txt = $status_labels[$old_data['Status']] ?? ($old_data['Status'] ?: 'Available');
        $new_status_txt = $status_labels[$table_status] ?? $table_status;
        $changes[] = "Status from '$old_status_txt' to '$new_status_txt'";
    }

    $stmt = $con->prepare("UPDATE tbltables SET TableName=?, ChairCount=?, Status=? WHERE ID=?");
    $stmt->bind_param("sisi", $table_name, $table_chairs, $table_status, $table_id);

    if ($stmt->execute()) {
        // Log user activity
        $user_id = $_SESSION['uid'];
        $change_list = !empty($changes) ? " (" . implode(', ', $changes) . ")" : "";
        $activity_desc = "Updated table details for: Table " . $old_name . $change_list;
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