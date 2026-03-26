<?php
include '../../config/db.php';

if (isset($_POST['save_tour'])) {
    $tour_name = $_POST['tour_name'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    
    // ເລື່ອງຮູບພາບ
    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $new_name = time() . '.' . $ext; // ຕັ້ງຊື່ໃໝ່ກັນຊ້ຳ
    $target = "../../assets/uploads/tours/" . $new_name;

    if (move_uploaded_file($tmp_name, $target)) {
        $sql = "INSERT INTO tours (tour_name, price, duration, image, status) 
                VALUES ('$tour_name', '$price', '$duration', '$new_name', 'Active')";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=success");
        }
    } else {
        echo "Error uploading image";
    }
}
?>