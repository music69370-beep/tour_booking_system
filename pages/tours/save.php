<?php
include '../../config/db.php';

if (isset($_POST['save_tour'])) {
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
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
        $sql = "INSERT INTO tours (tour_name, price, duration, itinerary, meals, activities, max_seats, image, status) 
                VALUES ('$tour_name', '$price', '$duration', '$itinerary', '$meals', '$activities', '$max_seats', '$new_name', 'Active')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=success");
        }
    } else {
        header("Location: index.php?msg=error");
    }
}
?>