<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['update_tour'])) {
    $id = $_POST['tour_id'];
    $tour_code = mysqli_real_escape_string($conn, $_POST['tour_code']);
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = $_POST['price'];
    
    // ຮັບຄ່າວັນທີ
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    
    // ຮັບຄ່າບໍລິການອື່ນໆ
    $max_seats = intval($_POST['max_seats']);
    $min_pax = intval($_POST['min_pax']);
    $meals = intval($_POST['meals']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $meeting_point = mysqli_real_escape_string($conn, $_POST['meeting_point']);
    $highlights = mysqli_real_escape_string($conn, $_POST['highlights']);
    $whats_included = mysqli_real_escape_string($conn, $_POST['whats_included']);
    $whats_excluded = mysqli_real_escape_string($conn, $_POST['whats_excluded']);
    $cancellation_policy = mysqli_real_escape_string($conn, $_POST['cancellation_policy']);
    $activities = mysqli_real_escape_string($conn, $_POST['activities']);
    $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']);
    
    $old_image = $_POST['old_image'];

    // ຈັດການຮູບພາບ
    $new_name = $old_image;
    if (!empty($_FILES['image']['name'])) {
        $new_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../../assets/uploads/tours/" . $new_name);
        if ($old_image && file_exists("../../assets/uploads/tours/" . $old_image)) {
            unlink("../../assets/uploads/tours/" . $old_image);
        }
    }

    // SQL ອັບເດດຂໍ້ມູນ (ບໍ່ມີ column cost_per_person ໃນ Query ນີ້)
    $sql = "UPDATE tours SET 
            tour_code = '$tour_code', 
            tour_name = '$tour_name', 
            category = '$category',
            price = '$price', 
            start_date = '$start_date', 
            end_date = '$end_date', 
            max_seats = '$max_seats', 
            min_pax = '$min_pax',
            meals = '$meals',
            duration = '$duration',
            meeting_point = '$meeting_point',
            highlights = '$highlights',
            whats_included = '$whats_included',
            whats_excluded = '$whats_excluded',
            cancellation_policy = '$cancellation_policy',
            activities = '$activities',
            itinerary = '$itinerary', 
            image = '$new_name'
            WHERE tour_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=updated");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>