<?php
include '../../config/db.php';

if (isset($_POST['update_tour'])) {
    $id = $_POST['tour_id'];
    $tour_name = $_POST['tour_name'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $status = $_POST['status'];
    $old_image = $_POST['old_image'];

    // ກວດເຊັກວ່າລູກຄ້າເລືອກຮູບໃໝ່ບໍ່?
    if ($_FILES['image']['name'] != "") {
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $new_name = time() . "_" . $image;
        move_uploaded_file($tmp_name, "../../assets/uploads/tours/" . $new_name);
        
        // ລຶບຮູບເກົ່າ
        if (file_exists("../../assets/uploads/tours/" . $old_image)) {
            unlink("../../assets/uploads/tours/" . $old_image);
        }
    } else {
        $new_name = $old_image; // ໃຊ້ຮູບເກົ່າ
    }

    $sql = "UPDATE tours SET 
            tour_name = '$tour_name', 
            price = '$price', 
            duration = '$duration', 
            image = '$new_name', 
            status = '$status' 
            WHERE tour_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=updated");
    }
}
?>