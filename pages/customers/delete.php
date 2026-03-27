<?php
include '../../config/db.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // ກວດສອບກ່ອນວ່າລູກຄ້ານີ້ມີການຈອງທົວຄ້າງໄວ້ບໍ່ (ເພື່ອປ້ອງກັນຂໍ້ມູນ Error)
    $check_booking = mysqli_query($conn, "SELECT booking_id FROM bookings WHERE customer_id = '$id'");
    
    if (mysqli_num_rows($check_booking) > 0) {
        // ຖ້າມີການຈອງ ຫ້າມລຶບ ແຕ່ໃຫ້ແຈ້ງເຕືອນ
        header("Location: index.php?msg=error");
    } else {
        // ຖ້າບໍ່ມີການຈອງ ສາມາດລຶບໄດ້ເລີຍ
        $sql = "DELETE FROM customers WHERE customer_id = '$id'";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=deleted");
            exit();
        }
    }
}
header("Location: index.php");
?>