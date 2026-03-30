<?php
include '../../config/db.php';

if (isset($_POST['save_tour'])) {
    // ຮັບຂໍ້ມູນທົວ
    $tour_code = mysqli_real_escape_string($conn, $_POST['tour_code']);
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $price = $_POST['price'];
    $cost = $_POST['cost_per_person'] ?: 0;
    $vehicle_id = $_POST['vehicle_id'];
    $guide_id = $_POST['guide_id'];
    $max_seats = $_POST['max_seats'];
    $min_pax = $_POST['min_pax'];
    $meals = $_POST['meals'];
    $meeting_point = mysqli_real_escape_string($conn, $_POST['meeting_point']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $highlights = mysqli_real_escape_string($conn, $_POST['highlights']);
    $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']);
    $whats_included = mysqli_real_escape_string($conn, $_POST['whats_included']);
    $whats_excluded = mysqli_real_escape_string($conn, $_POST['whats_excluded']);
    $cancel_policy = mysqli_real_escape_string($conn, $_POST['cancellation_policy']);
    $activities = mysqli_real_escape_string($conn, $_POST['activities']);

    // ຈັດການຮູບໜ້າປົກ
    $image_name = time() . "_" . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "../../assets/uploads/tours/" . $image_name);

    $sql = "INSERT INTO tours (tour_code, vehicle_id, guide_id, tour_name, category, price, cost_per_person, start_date, end_date, duration, meeting_point, itinerary, highlights, meals, activities, whats_included, whats_excluded, cancellation_policy, max_seats, min_pax, image, status) 
            VALUES ('$tour_code', '$vehicle_id', '$guide_id', '$tour_name', '$category', '$price', '$cost', '$start_date', '$end_date', '$duration', '$meeting_point', '$itinerary', '$highlights', '$meals', '$activities', '$whats_included', '$whats_excluded', '$cancel_policy', '$max_seats', '$min_pax', '$image_name', 'Active')";

    if (mysqli_query($conn, $sql)) {
        $tour_id = mysqli_insert_id($conn);
        // ຈັດການຮູບ Gallery
        if (!empty($_FILES['gallery']['name'][0])) {
            foreach ($_FILES['gallery']['tmp_name'] as $k => $tmp) {
                $g_name = time() . "_gal_$k_" . $_FILES['gallery']['name'][$k];
                if (move_uploaded_file($tmp, "../../assets/uploads/tours/" . $g_name)) {
                    mysqli_query($conn, "INSERT INTO tour_images (tour_id, image_name) VALUES ('$tour_id', '$g_name')");
                }
            }
        }
        header("Location: index.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>