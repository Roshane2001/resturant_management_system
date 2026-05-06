<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid']) || empty($_POST['order_id']) || empty($_POST['kot'])) {
    exit;
}

$order_id = intval($_POST['order_id']);
$kot = $_POST['kot'];

// Resolve Table Name or Order Type for logging from the database
$table_label = 'Unknown';
$stmt_order = $con->prepare("SELECT o.OrderType, t.TableName FROM tblorder o LEFT JOIN tbltables t ON o.TableID = t.ID WHERE o.ID = ?");
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$res_order = $stmt_order->get_result();
if ($order_data = $res_order->fetch_assoc()) {
    if ($order_data['OrderType'] === 'Take-away') {
        $table_label = 'Take-away';
    } else {
        $table_label = !empty($order_data['TableName']) ? "Table " . $order_data['TableName'] : 'Unknown Table';
    }
}
$stmt_order->close();

// Update all 'Ready' items in this KOT for this order to Received/Served (3)
$sql = "UPDATE tblorder_details SET order_status = 3 WHERE OrderID = ? AND KOT = ? AND order_status = 2";
$stmt = $con->prepare($sql);
$stmt->bind_param("is", $order_id, $kot);

if ($stmt->execute()) {
    // Log user activity for the served order
    $user_id = $_SESSION['uid'];
    $waiter_name = $_SESSION['name'] ?? 'Unknown Waitor';
    $activity_desc = "Waitor (" . $waiter_name . ") marked KOT #" . $kot . " as Received for " . $table_label;
    
    $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
    if ($log_stmt = $con->prepare($log_sql)) {
        $log_stmt->bind_param("is", $user_id, $activity_desc);
        $log_stmt->execute();
        $log_stmt->close();
    }
    echo "success";
}
$stmt->close();
?>