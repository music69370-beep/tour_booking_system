<?php
include '../../config/db.php';

if (isset($_POST['save_tour'])) {
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $vehicle_id = $_POST['vehicle_id']; // ຮັບຄ່າ ID ລົດ
    $price = $_POST['price'];
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']);
    $meals = $_POST['meals'];
    $activities = mysqli_real_escape_string($conn, $_POST['activities']);
    $max_seats = $_POST['max_seats'];
    
    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $new_name = time() . '.' . $ext;
    $target = "../../assets/uploads/tours/" . $new_name;

    if (move_uploaded_file($tmp_name, $target)) {
        // ເພີ່ມ vehicle_id ລົງໃນຄຳສັ່ງ SQL
        $sql = "INSERT INTO tours (vehicle_id, tour_name, price, duration, itinerary, meals, activities, max_seats, image, status) 
                VALUES ('$vehicle_id', '$tour_name', '$price', '$duration', '$itinerary', '$meals', '$activities', '$max_seats', '$new_name', 'Active')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=success");
        }
    } else {
        header("Location: index.php?msg=error");
    }
}
?>