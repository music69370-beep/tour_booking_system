<?php
include '../../config/db.php';

if (isset($_POST['save_tour'])) {
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $vehicle_id = $_POST['vehicle_id'];
    $guide_id = $_POST['guide_id'];
    $price = $_POST['price'];
    $cost = $_POST['cost_per_person']; // ຮັບຕົ້ນທຶນ
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']);
    $meals = $_POST['meals'];
    $activities = mysqli_real_escape_string($conn, $_POST['activities']);
    $max_seats = $_POST['max_seats'];
    
    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $new_name = time() . '.' . pathinfo($image, PATHINFO_EXTENSION);
    $target = "../../assets/uploads/tours/" . $new_name;

    if (move_uploaded_file($tmp_name, $target)) {
        // ບັນທຶກ SQL (ເພີ່ມ cost_per_person)
        $sql = "INSERT INTO tours (vehicle_id, guide_id, tour_name, price, cost_per_person, duration, itinerary, meals, activities, max_seats, image, status) 
                VALUES ('$vehicle_id', '$guide_id', '$tour_name', '$price', '$cost', '$duration', '$itinerary', '$meals', '$activities', '$max_seats', '$new_name', 'Active')";
        
        if (mysqli_query($conn, $sql)) {
            $tour_id = mysqli_insert_id($conn);
            if (!empty($_FILES['gallery']['name'][0])) {
                foreach ($_FILES['gallery']['tmp_name'] as $key => $tmp_gallery) {
                    $g_new_name = time() . "_gal_" . $key . "." . pathinfo($_FILES['gallery']['name'][$key], PATHINFO_EXTENSION);
                    if (move_uploaded_file($tmp_gallery, "../../assets/uploads/tours/" . $g_new_name)) {
                        mysqli_query($conn, "INSERT INTO tour_images (tour_id, image_name) VALUES ('$tour_id', '$g_new_name')");
                    }
                }
            }
            header("Location: index.php?msg=success");
        }
    }
}
?>