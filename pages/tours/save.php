<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_tour'])) {
    // ຮັບຂໍ້ມູນຈາກຟອມ
    $tour_code = mysqli_real_escape_string($conn, $_POST['tour_code']);
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $price = $_POST['price'];
    // ເອົາຕົວແປ $cost ອອກແລ້ວ
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

    // --- 1. Logic ກວດສອບວ່າ ໄກ້ຄົນນີ້ ຕິດວຽກທົວອື່ນໃນວັນທີນີ້ແລ້ວບໍ່? ---
    $check_guide = "SELECT COUNT(*) as busy FROM tours 
                    WHERE guide_id = '$guide_id' 
                    AND status = 'Active' 
                    AND (start_date <= '$end_date' AND end_date >= '$start_date')";
    
    $check_res = mysqli_query($conn, $check_guide);
    $is_busy = mysqli_fetch_assoc($check_res)['busy'];

    if ($is_busy > 0) {
        // ຖ້າໄກ້ບໍ່ວ່າງ ໃຫ້ສົ່ງກັບໄປໜ້າ add ພ້ອມແຈ້ງເຕືອນ
        header("Location: add.php?msg=guide_busy&s=$start_date&e=$end_date");
        exit();
    }

    // --- 2. ຖ້າໄກ້ວ່າງ: ດຳເນີນການອັບໂຫລດຮູບ ແລະ ບັນທຶກ ---
    $image_name = time() . "_" . $_FILES['image']['name'];
    if (move_uploaded_file($_FILES['image']['tmp_name'], "../../assets/uploads/tours/" . $image_name)) {
        
        // ປັບ SQL ໂດຍການເອົາ cost_per_person ອອກຈາກການ INSERT
        $sql = "INSERT INTO tours (tour_code, vehicle_id, guide_id, tour_name, category, price, start_date, end_date, duration, meeting_point, itinerary, highlights, meals, activities, whats_included, whats_excluded, cancellation_policy, max_seats, min_pax, image, status) 
                VALUES ('$tour_code', '$vehicle_id', '$guide_id', '$tour_name', '$category', '$price', '$start_date', '$end_date', '$duration', '$meeting_point', '$itinerary', '$highlights', '$meals', '$activities', '$whats_included', '$whats_excluded', '$cancel_policy', '$max_seats', '$min_pax', '$image_name', 'Active')";

        if (mysqli_query($conn, $sql)) {
            $tour_id = mysqli_insert_id($conn);
            // ຈັດການ Gallery (ຖ້າມີ)
            // ຈັດການ Gallery (ຖ້າມີ)
            if (!empty($_FILES['gallery']['name'][0])) {
                foreach ($_FILES['gallery']['tmp_name'] as $k => $tmp) {
                    // ແກ້ໄຂບ່ອນນີ້: ໃສ່ປີກກາ ຫຼື ໃຊ້ຈຸດຕໍ່ຂໍ້ຄວາມ
                    $g_name = time() . "_gal_" . $k . "_" . $_FILES['gallery']['name'][$k];
                    
                    if (move_uploaded_file($tmp, "../../assets/uploads/tours/" . $g_name)) {
                        mysqli_query($conn, "INSERT INTO tour_images (tour_id, image_name) VALUES ('$tour_id', '$g_name')");
                    }
                }
            }
            header("Location: index.php?msg=success");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Image upload failed";
    }
}
?>