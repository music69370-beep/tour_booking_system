<?php
include '../../config/db.php';

if (isset($_POST['update_tour'])) {
    $id = $_POST['tour_id'];
    $vehicle_id = $_POST['vehicle_id']; // ຮັບ ID ລົດໃໝ່
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $price = $_POST['price'];
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $max_seats = $_POST['max_seats'];
    $status = $_POST['status'];
    $old_image = $_POST['old_image'];

    // ... Logic ຈັດການຮູບພາບ (ຄືເກົ່າ) ...
    if ($_FILES['image']['name'] != "") {
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $new_name = time() . "_" . $image;
        move_uploaded_file($tmp_name, "../../assets/uploads/tours/" . $new_name);
        if (file_exists("../../assets/uploads/tours/" . $old_image)) unlink("../../assets/uploads/tours/" . $old_image);
    } else {
        $new_name = $old_image;
    }

    $sql = "UPDATE tours SET 
            vehicle_id = '$vehicle_id', 
            tour_name = '$tour_name', 
            price = '$price', 
            duration = '$duration', 
            max_seats = '$max_seats', 
            image = '$new_name', 
            status = '$status' 
            WHERE tour_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=updated");
    }
}
?>