<?php
include '../../config/db.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. ລຶບລາຍຊື່ຜູ້ຮ່ວມທາງກ່ອນ (ເພາະມັນເຊື່ອມຫາ booking_id)
    mysqli_query($conn, "DELETE FROM booking_participants WHERE booking_id = '$id'");

    // 2. ລຶບຂໍ້ມູນການຈ່າຍເງິນ (ຖ້າມີ)
    $res_slip = mysqli_query($conn, "SELECT payment_slip FROM payments WHERE booking_id = '$id'");
    $slip_data = mysqli_fetch_assoc($res_slip);
    if ($slip_data && !empty($slip_data['payment_slip'])) {
        unlink("../../assets/uploads/payments/" . $slip_data['payment_slip']);
    }
    mysqli_query($conn, "DELETE FROM payments WHERE booking_id = '$id'");

    // 3. ລຶບຂໍ້ມູນການຈອງ
    $sql = "DELETE FROM bookings WHERE booking_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=deleted");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>