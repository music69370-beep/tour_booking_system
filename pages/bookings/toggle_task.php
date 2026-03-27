<?php
include '../../config/db.php';

// ຮັບຄ່າຜ່ານ GET ຫຼື POST (ໃນທີ່ນີ້ໃຊ້ GET ຕາມຂອງເກົ່າເຈົ້າກ່ອນ)
if (isset($_GET['task_id']) && isset($_GET['status'])) {
    $task_id = mysqli_real_escape_string($conn, $_GET['task_id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $booking_id = mysqli_real_escape_string($conn, $_GET['booking_id']);

    // ອັບເດດຖານຂໍ້ມູນ
    $sql = "UPDATE booking_tasks SET is_completed = $status WHERE task_id = $task_id";
    
    if (mysqli_query($conn, $sql)) {
        // ສົ່ງຄ່າ JSON ກັບໄປບອກວ່າສຳເລັດ
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    exit();
}
?>