<?php
include '../../config/db.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. ດຶງຊື່ໄຟລ໌ຮູບພາບອອກມາກ່ອນຈະລຶບ
    $res = mysqli_query($conn, "SELECT driver_image, license_image FROM vehicles WHERE vehicle_id = '$id'");
    $data = mysqli_fetch_assoc($res);

    if ($data) {
        // 2. ລຶບໄຟລ໌ຮູບໃນ Folder
        $upload_path = "../../assets/uploads/vehicles/";
        if (!empty($data['driver_image']) && file_exists($upload_path . $data['driver_image'])) {
            unlink($upload_path . $data['driver_image']);
        }
        if (!empty($data['license_image']) && file_exists($upload_path . $data['license_image'])) {
            unlink($upload_path . $data['license_image']);
        }

        // 3. ລຶບຂໍ້ມູນໃນ Database
        $sql = "DELETE FROM vehicles WHERE vehicle_id = '$id'";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=deleted");
            exit();
        }
    }
}
header("Location: index.php");
?>