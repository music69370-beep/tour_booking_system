<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['update_tour'])) {
    $id = $_POST['tour_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $guide_id = $_POST['guide_id'];
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $price = $_POST['price'];
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $max_seats = $_POST['max_seats'];
    $status = $_POST['status'];
    $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']);
    $old_image = $_POST['old_image'];

    if (!empty($_FILES['image']['name'])) {
        $new_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../../assets/uploads/tours/" . $new_name);
        if (file_exists("../../assets/uploads/tours/" . $old_image)) unlink("../../assets/uploads/tours/" . $old_image);
    } else { $new_name = $old_image; }

    // ປັບ SQL ໂດຍການເອົາ cost_per_person ອອກ
    $sql = "UPDATE tours SET 
            vehicle_id = '$vehicle_id', 
            guide_id = '$guide_id',
            tour_name = '$tour_name', 
            price = '$price', 
            duration = '$duration', 
            max_seats = '$max_seats', 
            itinerary = '$itinerary',
            image = '$new_name', 
            status = '$status' 
            WHERE tour_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=updated");
    }
}
?>