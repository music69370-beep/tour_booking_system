<?php
include '../../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. ດຶງຊື່ຮູບພາບມາກ່ອນເພື່ອຈະລຶບໄຟລ໌
    $res = mysqli_query($conn, "SELECT image FROM tours WHERE tour_id = $id");
    $data = mysqli_fetch_assoc($res);
    $image_name = $data['image'];

    // 2. ລຶບຂໍ້ມູນໃນ Database
    $sql = "DELETE FROM tours WHERE tour_id = $id";
    if (mysqli_query($conn, $sql)) {
        // 3. ລຶບໄຟລ໌ຮູບພາບອອກຈາກ Folder
        if (file_exists("../../assets/uploads/tours/" . $image_name)) {
            unlink("../../assets/uploads/tours/" . $image_name);
        }
        header("Location: index.php?msg=deleted");
    }
}
?>