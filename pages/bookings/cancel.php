<?php
include '../../config/db.php';
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';

    if (mysqli_query($conn, "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = '$id'")) {
        header("Location: index.php?msg=updated&status=$status");
        exit(); // ຕ້ອງມີ exit ປ້ອງກັນ script ເຮັດວຽກຕໍ່
    }
}
header("Location: index.php");
exit();
?>