<?php
include('../include/dbconnection.php');

$last_id = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;

// Find new records
$query = mysqli_query($con, "SELECT ID, OrderID, KOT FROM tblorder_details WHERE ID > $last_id AND order_status = 0 ORDER BY ID ASC");
$new_records = [];
while ($row = mysqli_fetch_assoc($query)) {
    $new_records[] = $row;
}

// Get the counts and fetch branding sound
$count_query = mysqli_query($con, "
    SELECT 
        (SELECT COUNT(DISTINCT OrderID, KOT) FROM tblorder_details WHERE order_status = 0) as pending_count,
        (SELECT COUNT(DISTINCT OrderID, KOT) FROM tblorder_details WHERE order_status = 1) as processing_count,
        (SELECT notification_sound FROM tblbranding LIMIT 1) as sound
");
$counts = mysqli_fetch_assoc($count_query);
$pending_count = $counts['pending_count'] ? intval($counts['pending_count']) : 0;
$processing_count = $counts['processing_count'] ? intval($counts['processing_count']) : 0;
$notif_sound = $counts['sound'];

if (count($new_records) > 0) {
    // Process one order at a time to generate single KOTs
    $order_id = $new_records[0]['OrderID'];
    $kot_num = $new_records[0]['KOT'];
    $ids = [];
    $max_id_for_this_order = $last_id;
    
    foreach ($new_records as $rec) {
        if ($rec['OrderID'] == $order_id) {
            $ids[] = $rec['ID'];
            $max_id_for_this_order = $rec['ID'];
        }
    }
    
    echo json_encode([
        'has_new' => true, 
        'new_max_id' => $max_id_for_this_order,
        'order_id' => $order_id,
        'ids' => implode(',', $ids),
        'kot_num' => $kot_num,
        'pending_count' => $pending_count,
        'processing_count' => $processing_count,
        'sound' => $notif_sound
    ]);
} else {
    echo json_encode([
        'has_new' => false,
        'pending_count' => $pending_count,
        'processing_count' => $processing_count,
        'sound' => $notif_sound
    ]);
}
?>
