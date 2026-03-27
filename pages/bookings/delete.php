<?php
include '../../config/db.php';
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $status = isset($_GET['status']) ? $_GET['status'] : 'all'; // ຮັບຄ່າ status ເດີມ

    // ລຶບຂໍ້ມູນ (Logic ເດີມ)
    mysqli_query($conn, "DELETE FROM booking_participants WHERE booking_id = '$id'");
    mysqli_query($conn, "DELETE FROM payments WHERE booking_id = '$id'");
    
    if (mysqli_query($conn, "DELETE FROM bookings WHERE booking_id = '$id'")) {
        header("Location: index.php?msg=deleted&status=$status"); // ສົ່ງຄ່າ status ກັບຄືນ
        exit();
    }
}
?>