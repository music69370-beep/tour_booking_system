<?php
include '../../config/db.php';
/** @var mysqli $conn */
// ປິດ Error ທີ່ບໍ່ຈຳເປັນເພື່ອໃຫ້ JSON ອອກມາສະອາດ
error_reporting(0);
header('Content-Type: application/json');

if(isset($_GET['task_id']) && isset($_GET['status'])) {
    $task_id = mysqli_real_escape_string($conn, $_GET['task_id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']); // 0 ຫຼື 1
    
    // ອັບເດດສະຖານະວຽກ
    $sql = "UPDATE booking_tasks SET is_completed = '$status' WHERE task_id = '$task_id'";
    
    if(mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['status' => 'invalid_request']);
}
exit();
?>