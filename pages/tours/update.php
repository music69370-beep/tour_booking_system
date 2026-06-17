<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['update_tour'])) {
    $id = $_POST['tour_id'];
    $tour_code = mysqli_real_escape_string($conn, $_POST['tour_code']);
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $price = $_POST['price'];
    $max_seats = intval($_POST['max_seats']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']); // ຮັບ JSON ມາແລ້ວ
    $old_image = $_POST['old_image'];

    // ຈັດການຮູບພາບ (ຄືເກົ່າ)
    if (!empty($_FILES['image']['name'])) {
        $new_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../../assets/uploads/tours/" . $new_name);
        if (file_exists("../../assets/uploads/tours/" . $old_image)) unlink("../../assets/uploads/tours/" . $old_image);
    } else { $new_name = $old_image; }

    $sql = "UPDATE tours SET 
            tour_code = '$tour_code', tour_name = '$tour_name', price = '$price', 
            start_date = '$start_date', end_date = '$end_date', max_seats = '$max_seats', 
            itinerary = '$itinerary', image = '$new_name'
            WHERE tour_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=updated");
    }
}
?>