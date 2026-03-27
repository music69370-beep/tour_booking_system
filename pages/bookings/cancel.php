<?php
include '../../config/db.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // ອັບເດດສະຖານະເປັນ Cancelled
    $sql = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=updated");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>