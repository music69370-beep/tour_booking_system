<?php
include '../../config/db.php';

if (isset($_POST['save_tour'])) {
    $tour_code = mysqli_real_escape_string($conn, $_POST['tour_code']);
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $vehicle_id = $_POST['vehicle_id'];
    $guide_id = $_POST['guide_id'];
    $price = $_POST['price'];
    $cost = $_POST['cost_per_person'] ?: 0;
    $min_pax = $_POST['min_pax'] ?: 1;
    $max_seats = $_POST['max_seats'];
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $meeting_point = mysqli_real_escape_string($conn, $_POST['meeting_point']);
    $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']);
    $highlights = mysqli_real_escape_string($conn, $_POST['highlights']);
    $meals = $_POST['meals'] ?: 0;
    $activities = mysqli_real_escape_string($conn, $_POST['activities']);
    $included = mysqli_real_escape_string($conn, $_POST['whats_included']);
    $excluded = mysqli_real_escape_string($conn, $_POST['whats_excluded']);
    $cancel_policy = mysqli_real_escape_string($conn, $_POST['cancellation_policy']);
    $status = isset($_POST['status']) ? 'Active' : 'Inactive';

    // ຈັດການຮູບພາບ Cover
    $image = $_FILES['image']['name'];
    $new_name = time() . '.' . pathinfo($image, PATHINFO_EXTENSION);
    move_uploaded_file($_FILES['image']['tmp_name'], "../../assets/uploads/tours/" . $new_name);

    $sql = "INSERT INTO tours (
        tour_code, vehicle_id, guide_id, tour_name, category, price, cost_per_person, 
        duration, meeting_point, itinerary, highlights, meals, activities, 
        whats_included, whats_excluded, cancellation_policy, max_seats, min_pax, image, status
    ) VALUES (
        '$tour_code', '$vehicle_id', '$guide_id', '$tour_name', '$category', '$price', '$cost', 
        '$duration', '$meeting_point', '$itinerary', '$highlights', '$meals', '$activities', 
        '$included', '$excluded', '$cancel_policy', '$max_seats', '$min_pax', '$new_name', '$status'
    )";

    if (mysqli_query($conn, $sql)) {
        $tour_id = mysqli_insert_id($conn);
        // Gallery
        if (!empty($_FILES['gallery']['name'][0])) {
            foreach ($_FILES['gallery']['tmp_name'] as $key => $tmp) {
                $g_name = time() . "_gal_$key." . pathinfo($_FILES['gallery']['name'][$key], PATHINFO_EXTENSION);
                if (move_uploaded_file($tmp, "../../assets/uploads/tours/$g_name")) {
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