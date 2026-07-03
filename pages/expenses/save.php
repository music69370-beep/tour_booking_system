<?php
include '../../config/db.php';
/** @var mysqli $conn */

// ປ່ຽນຈາກ save_expense ເປັນ btn_save ໃຫ້ຕົງກັບປຸ່ມໃນ index.php
if (isset($_POST['btn_save'])) {
    
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    
    // ປ່ຽນຈາກ description ເປັນ note ໃຫ້ຕົງກັບຊື່ຊ່ອງໃນຟອມ
    $desc = mysqli_real_escape_string($conn, $_POST['note']);

    $sql = "INSERT INTO tour_expenses (tour_id, travel_date, category, amount, description) 
            VALUES ('$tour_id', '$travel_date', '$category', '$amount', '$desc')";

    if (mysqli_query($conn, $sql)) {
        // ບັນທຶກສຳເລັດ ໃຫ້ກັບໄປໜ້າຫຼັກ ພ້ອມແຈ້ງເຕືອນ success
        header("Location: index.php?msg=success");
        exit();
    } else {
        // ຖ້າມີ Error ໃຫ້ໂຊອອກມາເລີຍ
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // ຖ້າເຂົ້າໜ້ານີ້ໂດຍບໍ່ໄດ້ກົດປຸ່ມ ໃຫ້ສົ່ງກັບໄປໜ້າລາຍການ
    header("Location: index.php");
    exit();
}
?>