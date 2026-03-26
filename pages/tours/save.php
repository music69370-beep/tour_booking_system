<?php
include '../../config/db.php';

if (isset($_POST['save_tour'])) {
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $price = $_POST['price'];
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $max_seats = $_POST['max_seats']; // ຮັບຄ່າໃໝ່
    
    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $new_name = time() . '.' . $ext;
    $target = "../../assets/uploads/tours/" . $new_name;

    if (move_uploaded_file($tmp_name, $target)) {
        // ເພີ່ມ max_seats ລົງໃນ SQL
        $sql = "INSERT INTO tours (tour_name, price, duration, max_seats, image, status) 
                VALUES ('$tour_name', '$price', '$duration', '$max_seats', '$new_name', 'Active')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=success");
        }
    } else {
        header("Location: index.php?msg=error");
    }
}
?>