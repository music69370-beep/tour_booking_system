<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. ດຶງຊື່ຮູບພາບທັງໝົດອອກມາກ່ອນເພື່ອລຶບໄຟລ໌ໃນ Folder
    $res = mysqli_query($conn, "SELECT image, license_image, id_card_image FROM drivers WHERE driver_id = '$id'");
    $data = mysqli_fetch_assoc($res);

    if ($data) {
        $upload_dir = "../../assets/uploads/drivers/";
        
        // ລຶບຮູບໂປຣຟາຍ
        if (!empty($data['image']) && file_exists($upload_dir . $data['image'])) {
            unlink($upload_dir . $data['image']);
        }
        // ລຶບຮູບໃບຂັບຂີ່
        if (!empty($data['license_image']) && file_exists($upload_dir . $data['license_image'])) {
            unlink($upload_dir . $data['license_image']);
        }
        // ລຶບຮູບບັດປະຈຳຕົວ
        if (!empty($data['id_card_image']) && file_exists($upload_dir . $data['id_card_image'])) {
            unlink($upload_dir . $data['id_card_image']);
        }

        // 2. ລຶບຂໍ້ມູນອອກຈາກຖານຂໍ້ມູນ
        $sql = "DELETE FROM drivers WHERE driver_id = '$id'";
        if (mysqli_query($conn, $sql)) {
            // ລຶບສຳເລັດ ສົ່ງກັບໄປໜ້າຫຼັກພ້ອມແຈ້ງເຕືອນ
            header("Location: index.php?msg=deleted");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>