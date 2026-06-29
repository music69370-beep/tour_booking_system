<?php
include '../../config/db.php';
/** @var mysqli $conn */
if (isset($_GET['img_id']) && isset($_GET['tour_id'])) {
    $img_id = $_GET['img_id'];
    $tour_id = $_GET['tour_id'];

    // 1. ດຶງຊື່ຮູບອອກມາກ່ອນຈະລຶບ
    $res = mysqli_query($conn, "SELECT image_name FROM tour_images WHERE image_id = '$img_id'");
    $data = mysqli_fetch_assoc($res);

    if ($data) {
        // 2. ລຶບໄຟລ໌ໃນ Folder
        if (file_exists("../../assets/uploads/tours/" . $data['image_name'])) {
            unlink("../../assets/uploads/tours/" . $data['image_name']);
        }
        // 3. ລຶບຂໍ້ມູນໃນ DB
        mysqli_query($conn, "DELETE FROM tour_images WHERE image_id = '$img_id'");
    }
    
    header("Location: edit.php?id=$tour_id&msg=deleted");
}
?>