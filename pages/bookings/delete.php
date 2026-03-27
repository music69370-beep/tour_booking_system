<?php
include '../../config/db.php';
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';

    mysqli_query($conn, "DELETE FROM booking_participants WHERE booking_id = '$id'");
    mysqli_query($conn, "DELETE FROM payments WHERE booking_id = '$id'");
    
    if (mysqli_query($conn, "DELETE FROM bookings WHERE booking_id = '$id'")) {
        header("Location: index.php?msg=deleted&status=$status");
        exit();
    }
}
header("Location: index.php");
exit();
?>