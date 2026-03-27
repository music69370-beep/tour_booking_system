<?php
include '../../config/db.php';

if (isset($_POST['confirm_cancel'])) {
    $id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $refund = mysqli_real_escape_string($conn, $_POST['refund_amount']);
    $cost = mysqli_real_escape_string($conn, $_POST['cancellation_cost']);
    $reason = mysqli_real_escape_string($conn, $_POST['cancel_reason']);

    $sql = "UPDATE bookings SET 
            status = 'Cancelled', 
            refund_amount = '$refund', 
            cancellation_cost = '$cost', 
            cancel_reason = '$reason' 
            WHERE booking_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=updated&status=Cancelled");
        exit();
    }
}
?>