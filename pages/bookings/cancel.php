<?php
include '../../config/db.php';
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $status = isset($_GET['status']) ? $_GET['status'] : 'all'; // ຮັບຄ່າ status ເດີມ

    if (mysqli_query($conn, "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = '$id'")) {
        header("Location: index.php?msg=updated&status=$status"); // ສົ່ງຄ່າ status ກັບຄືນ
        exit();
    }
}
?>