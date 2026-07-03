<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['btn_save'])) {
    $t_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $s_date = $_POST['start_date'];
    $r_date = $_POST['return_date'];
    
    // ຮັບຄ່າເປັນ Array
    $v_ids = $_POST['vehicle_ids'];
    $d_ids = $_POST['driver_ids'];
    $mileages = $_POST['start_mileages'];

    if (!empty($v_ids) && is_array($v_ids)) {
        foreach ($v_ids as $index => $v_id) {
            $v_id = mysqli_real_escape_string($conn, $v_id);
            $d_id = mysqli_real_escape_string($conn, $d_ids[$index]);
            $mile = mysqli_real_escape_string($conn, $mileages[$index]);

            // 1. ບັນທຶກລົງຕາຕະລາງ vehicle_outings
            $sql = "INSERT INTO vehicle_outings (vehicle_id, tour_id, driver_id, start_date, return_date, start_mileage, status) 
                    VALUES ('$v_id', '$t_id', '$d_id', '$s_date', '$r_date', '$mile', 'On Trip')";

            if (mysqli_query($conn, $sql)) {
                // 2. ອັບເດດສະຖານະລົດໃຫ້ Busy
                mysqli_query($conn, "UPDATE vehicles SET status = 'Busy' WHERE vehicle_id = '$v_id'");
                
                // 3. ອັບເດດສະຖານະຄົນຂັບໃຫ້ Busy
                mysqli_query($conn, "UPDATE drivers SET status = 'Busy' WHERE driver_id = '$d_id'");
            }
        }
        
        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "ກະລຸນາເລືອກລົດຢ່າງໜ້ອຍ 1 ຄັນ";
    }
}
?>