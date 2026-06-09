<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_GET['id']) && isset($_GET['end_mileage'])) {
    $id = $_GET['id'];
    $end_mile = $_GET['end_mileage'];

    // 1. ດຶງ vehicle_id ແລະ driver_id ຈາກ Trip ນີ້ອອກມາກ່ອນ
    $res = mysqli_query($conn, "SELECT vehicle_id, driver_id FROM vehicle_outings WHERE outing_id = '$id'");
    $data = mysqli_fetch_assoc($res);
    $v_id = $data['vehicle_id'];
    $d_id = $data['driver_id'];

    // 2. ອັບເດດ Trip ໃຫ້ສຳເລັດ
    $sql_update = "UPDATE vehicle_outings SET end_mileage = '$end_mile', status = 'Completed' WHERE outing_id = '$id'";
    
    if (mysqli_query($conn, $sql_update)) {
        // 3. ປ່ອຍ "ລົດ" ແລະ "ຄົນຂັບ" ໃຫ້ Available (ວ່າງ) ອີກຄັ້ງ
        mysqli_query($conn, "UPDATE vehicles SET status = 'Available' WHERE vehicle_id = '$v_id'");
        mysqli_query($conn, "UPDATE drivers SET status = 'Available' WHERE driver_id = '$d_id'");
        
        header("Location: index.php?msg=updated");
        exit();
    }
}
?>