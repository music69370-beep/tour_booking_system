<?php
include '../../config/db.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // ເລີ່ມ Transaction ເພື່ອຄວາມປອດໄພ
    mysqli_begin_transaction($conn);

    try {
        // 1. ລຶບລາຍຊື່ຜູ້ຮ່ວມທາງ ທີ່ຜູກກັບການຈອງຂອງລູກຄ້ານີ້
        mysqli_query($conn, "DELETE FROM booking_participants WHERE booking_id IN (SELECT booking_id FROM bookings WHERE customer_id = '$id')");

        // 2. ລຶບປະຫວັດການຈ່າຍເງິນ ຂອງລູກຄ້ານີ້
        mysqli_query($conn, "DELETE FROM payments WHERE booking_id IN (SELECT booking_id FROM bookings WHERE customer_id = '$id')");

        // 3. ລຶບການຈອງ (Bookings) ຂອງລູກຄ້ານີ້
        mysqli_query($conn, "DELETE FROM bookings WHERE customer_id = '$id'");

        // 4. ລຶບຕົວລູກຄ້າ (Customer)
        mysqli_query($conn, "DELETE FROM customers WHERE customer_id = '$id'");

        // ຢືນຢັນການລຶບທັງໝົດ
        mysqli_commit($conn);
        header("Location: index.php?msg=deleted");
        exit();

    } catch (Exception $e) {
        // ຖ້າຜິດພາດ ໃຫ້ຍົກເລີກທຸກຢ່າງ
        mysqli_rollback($conn);
        header("Location: index.php?msg=error");
        exit();
    }
}
header("Location: index.php");
?>