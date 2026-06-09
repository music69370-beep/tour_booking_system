<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['btn_save'])) {
    $v_id = $_POST['vehicle_id'];
    $d_id = $_POST['driver_id'];
    $t_id = $_POST['tour_id'];
    $s_date = $_POST['start_date'];
    $r_date = $_POST['return_date'];
    $mile = $_POST['start_mileage'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    // 1. ບັນທຶກລົງຕາຕະລາງ outings
    $sql = "INSERT INTO vehicle_outings (vehicle_id, tour_id, driver_id, start_date, return_date, start_mileage, notes, status) 
            VALUES ('$v_id', '$t_id', '$d_id', '$s_date', '$r_date', '$mile', '$notes', 'On Trip')";

    if (mysqli_query($conn, $sql)) {
        // 2. ອັບເດດສະຖານະລົດ ໃຫ້ Busy
        mysqli_query($conn, "UPDATE vehicles SET status = 'Busy' WHERE vehicle_id = '$v_id'");
        
        // 3. ອັບເດດສະຖານະຄົນຂັບ ໃຫ້ Busy
        mysqli_query($conn, "UPDATE drivers SET status = 'Busy' WHERE driver_id = '$d_id'");
        
        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>