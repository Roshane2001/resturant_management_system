<?php
session_start();
include('../include/dbconnection.php');

header('Content-Type: application/json');

if (empty($_SESSION['uid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['new_items'])) {
    $order_id = intval($_POST['order_id']);
    $new_items = json_decode($_POST['new_items'], true);
    $inserted_detail_ids = [];

    if ($order_id <= 0 || empty($new_items)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid order ID or empty items list.']);
        exit;
    }

    mysqli_begin_transaction($con);

    try {
        $currentTime = date('h:i:s A');
        $today = date('Y-m-d');

        // Generate sequential KOT number for today
        $stmt_kot = $con->prepare("SELECT MAX(CAST(KOT AS UNSIGNED)) FROM tblorder_details WHERE OrderDate = ?");
        $stmt_kot->bind_param("s", $today);
        $stmt_kot->execute();
        $stmt_kot->bind_result($max_kot);
        $stmt_kot->fetch();
        $stmt_kot->close();
        $kot_num = $max_kot ? $max_kot + 1 : 1;

        // Set status to 1 (Processing) as the KOT is being printed immediately from POS
        $stmt_detail = $con->prepare("INSERT INTO tblorder_details (OrderID, ProductID, Qty, Price, KOT, OrderDate, OrderTime, order_status, staff_id) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)");
        $staff_id = intval($_SESSION['uid']);

        foreach ($new_items as $item) {
            $pid = intval($item['id']);
            $qty = intval($item['qty']);
            $price = floatval($item['price']);

            $stmt_detail->bind_param("iiidsssi", $order_id, $pid, $qty, $price, $kot_num, $today, $currentTime, $staff_id);
            if (!$stmt_detail->execute()) {
                throw new Exception("Failed to insert order detail: " . $stmt_detail->error);
            }

            // Deduct stock for countable products
            $stmt_stock = $con->prepare("UPDATE tblproducts SET Quantity = Quantity - ? WHERE ID = ? AND Type = 'Countable'");
            $stmt_stock->bind_param("ii", $qty, $pid);
            $stmt_stock->execute();
            $stmt_stock->close();

            $inserted_detail_ids[] = $stmt_detail->insert_id;
        }
        $stmt_detail->close();

        mysqli_commit($con);
        echo json_encode(['status' => 'success', 'detail_ids' => $inserted_detail_ids, 'kot_num' => $kot_num]);

    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    
    $con->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method or missing data.']);
}
?>