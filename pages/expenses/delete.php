<?php
include '../../config/db.php';
/** @var mysqli $conn */

// ກວດສອບວ່າມີການສົ່ງ ID ມາຫຼືບໍ່
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // ຄຳສັ່ງລຶບຂໍ້ມູນຈາກຕາຕະລາງ tour_expenses
    $sql = "DELETE FROM tour_expenses WHERE expense_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        // ລຶບສຳເລັດ ໃຫ້ກັບໄປໜ້າຫຼັກ ພ້ອມແຈ້ງເຕືອນ deleted
        header("Location: index.php?msg=deleted");
        exit();
    } else {
        // ຖ້າມີ Error
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // ຖ້າບໍ່ມີ ID ສົ່ງມາ ໃຫ້ກັບໄປໜ້າ index
    header("Location: index.php");
    exit();
}
?>