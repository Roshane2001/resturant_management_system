<?php
include('../include/dbconnection.php');

header('Content-Type: application/json');

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$table_id = isset($_GET['table_id']) ? intval($_GET['table_id']) : 0;

if ($order_id > 0 || $table_id > 0) {
    if ($order_id > 0) {
        $query = "SELECT ID, Advance, DamageClaim, OrderType FROM tblorder WHERE ID = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $order_id);
    } else {
        $query = "SELECT ID, Advance, DamageClaim, OrderType FROM tblorder WHERE TableID = ? AND Status = 'Pending' LIMIT 1";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $table_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $order_id = $row['ID'];
        
        $details_query = "SELECT d.ID as detail_id, d.ProductID as id, p.ProductName as name, d.Price as price, d.Qty as qty, d.staff_id, d.order_status
                          FROM tblorder_details d 
                          JOIN tblproducts p ON d.ProductID = p.ID 
                          WHERE d.OrderID = ?";
        $stmt_d = $con->prepare($details_query);
        $stmt_d->bind_param("i", $order_id);
        $stmt_d->execute();
        $details_result = $stmt_d->get_result();
        
        $items = [];
        while ($item = $details_result->fetch_assoc()) {
            $item['id'] = intval($item['id']);
            $item['price'] = floatval($item['price']);
            $item['qty'] = intval($item['qty']);
            $item['staff_id'] = intval($item['staff_id']);
            $item['detail_id'] = intval($item['detail_id']);
            $item['order_status'] = intval($item['order_status']);
            $items[] = $item;
        }
        
        echo json_encode([
            'status' => 'success', 
            'items' => $items, 
            'order_id' => $order_id,
            'order_type' => $row['OrderType'],
            'advance' => floatval($row['Advance']),
            'damage_claim' => floatval($row['DamageClaim'])
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No active order found']);
    }
}
?>