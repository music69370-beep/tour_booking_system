<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_vehicle'])) {
    // 1. ຮັບຂໍ້ມູນລົດ
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $plate = mysqli_real_escape_string($conn, $_POST['plate_number']);
    $type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $cap = intval($_POST['capacity']);
    $ins_expiry = $_POST['insurance_expiry'];
    $amenities = mysqli_real_escape_string($conn, $_POST['amenities']);

    // 2. ບັນທຶກລົງຕາຕະລາງ vehicles (ບໍ່ມີ Column ຄົນຂັບ)
    $sql = "INSERT INTO vehicles (
        plate_number, model, vehicle_type, capacity, insurance_expiry, amenities, status
    ) VALUES (
        '$plate', '$model', '$type', $cap, '$ins_expiry', '$amenities', 'Available'
    )";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>