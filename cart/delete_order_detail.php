<?php
session_start();
include('../include/dbconnection.php');

header('Content-Type: application/json');

if (empty($_SESSION['uid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['detail_id'])) {
    $detail_id = intval($_POST['detail_id']);

    // Security: Only allow deletion if status is 0 (before processing/printing in POS)
    $stmt = $con->prepare("DELETE FROM tblorder_details WHERE ID = ? AND order_status = 0");
    $stmt->bind_param("i", $detail_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Item cannot be deleted. It may already be in processing.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
    $con->close();
}
?>