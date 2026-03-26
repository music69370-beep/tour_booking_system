<?php
include '../../config/db.php';

if (isset($_POST['save_tour'])) {
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $vehicle_id = $_POST['vehicle_id'];
    $price = $_POST['price'];
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']);
    $meals = $_POST['meals'];
    $activities = mysqli_real_escape_string($conn, $_POST['activities']);
    $max_seats = $_POST['max_seats'];
    
    // 1. ຈັດການຮູບໜ້າປົກ
    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $new_name = time() . '.' . $ext;
    $target = "../../assets/uploads/tours/" . $new_name;

    if (move_uploaded_file($tmp_name, $target)) {
        // 2. ບັນທຶກຂໍ້ມູນທົວ
        $sql = "INSERT INTO tours (vehicle_id, tour_name, price, duration, itinerary, meals, activities, max_seats, image, status) 
                VALUES ('$vehicle_id', '$tour_name', '$price', '$duration', '$itinerary', '$meals', '$activities', '$max_seats', '$new_name', 'Active')";
        
        if (mysqli_query($conn, $sql)) {
            $tour_id = mysqli_insert_id($conn);

            // 3. ຈັດການຮູບ Gallery (ຖ້າມີການເລືອກຮູບ)
            if (!empty($_FILES['gallery']['name'][0])) {
                foreach ($_FILES['gallery']['tmp_name'] as $key => $tmp_gallery) {
                    $g_name = $_FILES['gallery']['name'][$key];
                    $g_ext = pathinfo($g_name, PATHINFO_EXTENSION);
                    $g_new_name = time() . "_gal_" . $key . "." . $g_ext;
                    $g_target = "../../assets/uploads/tours/" . $g_new_name;

                    if (move_uploaded_file($tmp_gallery, $g_target)) {
                        mysqli_query($conn, "INSERT INTO tour_images (tour_id, image_name) VALUES ('$tour_id', '$g_new_name')");
                    }
                }
            }
            header("Location: index.php?msg=success");
        }
    } else {
        header("Location: index.php?msg=error");
    }
}
?>