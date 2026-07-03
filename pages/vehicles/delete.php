<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. ກວດສອບວ່າມີລົດຄັນນີ້ແທ້ຫຼືບໍ່
    $check = mysqli_query($conn, "SELECT vehicle_id FROM vehicles WHERE vehicle_id = '$id'");
    
    if (mysqli_num_rows($check) > 0) {
        // 2. ລຶບຂໍ້ມູນໃນ Database 
        // (ລະບົບຈະລຶບຂໍ້ມູນໃນ vehicle_outings ໃຫ້ເອງຖ້າເຈົ້າຕັ້ງ ON DELETE CASCADE ໄວ້)
        $sql = "DELETE FROM vehicles WHERE vehicle_id = '$id'";
        
        if (mysqli_query($conn, $sql)) {
            // ລຶບສຳເລັດ ກັບໄປໜ້າຫຼັກ
            header("Location: index.php?msg=deleted");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// ຖ້າບໍ່ມີ ID ຫຼື ຜິດພາດ ໃຫ້ກັບໄປໜ້າ index
header("Location: index.php");
exit();
?>