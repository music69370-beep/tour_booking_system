<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_tour'])) {
    // 1. ຮັບຂໍ້ມູນຈາກຟອມ
    $tour_code = mysqli_real_escape_string($conn, $_POST['tour_code']);
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $price = $_POST['price'];
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $max_seats = intval($_POST['max_seats']);
    $meals = intval($_POST['meals']);
    $meeting_point = mysqli_real_escape_string($conn, $_POST['meeting_point']);
    $highlights = mysqli_real_escape_string($conn, $_POST['highlights']);
    $whats_included = mysqli_real_escape_string($conn, $_POST['whats_included']);
    $whats_excluded = mysqli_real_escape_string($conn, $_POST['whats_excluded']);
    $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']);

    // 2. ຈັດການອັບໂຫລດຮູບໜ້າປົກ
    $image_name = "";
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../../assets/uploads/tours/" . $image_name);
    }

    // 3. ບັນທຶກລົງ Database
    $sql = "INSERT INTO tours (
                tour_code, tour_name, category, price, start_date, end_date, 
                duration, max_seats, meals, meeting_point, highlights, 
                whats_included, whats_excluded, itinerary, image, status
            ) VALUES (
                '$tour_code', '$tour_name', '$category', '$price', '$start_date', '$end_date', 
                '$duration', $max_seats, $meals, '$meeting_point', '$highlights', 
                '$whats_included', '$whats_excluded', '$itinerary', '$image_name', 'Active'
            )";

    if (mysqli_query($conn, $sql)) {
        $tour_id = mysqli_insert_id($conn);
        
        // 4. ຈັດການອັບໂຫລດຮູບ Gallery
        if (!empty($_FILES['gallery']['name'][0])) {
            foreach ($_FILES['gallery']['tmp_name'] as $k => $tmp) {
                if(!empty($tmp)) {
                    $g_name = time() . "_gal_" . $k . "_" . $_FILES['gallery']['name'][$k];
                    if (move_uploaded_file($tmp, "../../assets/uploads/tours/" . $g_name)) {
                        mysqli_query($conn, "INSERT INTO tour_images (tour_id, image_name) VALUES ($tour_id, '$g_name')");
                    }
                }
            }
        }

        // --- ຈຸດທີ່ແກ້ໄຂ: ໃຫ້ Redirect ກັບມາໜ້າ add.php ແທນ index.php ---
        header("Location: add.php?msg=success");
        exit();
        
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>