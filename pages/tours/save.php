<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_tour'])) {
    $tour_code = mysqli_real_escape_string($conn, $_POST['tour_code']);
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $category  = mysqli_real_escape_string($conn, $_POST['category']);
    $price     = $_POST['price'];
    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];
    $max_seats = intval($_POST['max_seats']);
    $meeting_point = mysqli_real_escape_string($conn, $_POST['meeting_point'] ?? '');
    $highlights    = mysqli_real_escape_string($conn, $_POST['highlights'] ?? '');
    $whats_included = mysqli_real_escape_string($conn, $_POST['whats_included'] ?? '');
    $whats_excluded = mysqli_real_escape_string($conn, $_POST['whats_excluded'] ?? '');
    $itinerary      = mysqli_real_escape_string($conn, $_POST['itinerary'] ?? '[]');

    // ຈັດການຮູບພາບ (ຮັກສາໄວ້ຄືເກົ່າ)
    $image_name = "";
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = "../../assets/uploads/tours/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
    }

    // ບັນທຶກລົງ Table tours
    $sql = "INSERT INTO tours (tour_code, tour_name, category, price, start_date, end_date, max_seats, meeting_point, highlights, whats_included, whats_excluded, itinerary, image, status) 
            VALUES ('$tour_code', '$tour_name', '$category', '$price', '$start_date', '$end_date', '$max_seats', '$meeting_point', '$highlights', '$whats_included', '$whats_excluded', '$itinerary', '$image_name', 'Active')";

    if (mysqli_query($conn, $sql)) {
        $tour_id = mysqli_insert_id($conn);
        
        // --- ບັນທຶກລາຍຊື່ໄກ້ຫຼາຍຄົນ (Multiple Guides Assignment) ---
        if (!empty($_POST['guide_ids']) && is_array($_POST['guide_ids'])) {
            foreach ($_POST['guide_ids'] as $gid) {
                $gid = intval($gid);
                // ບັນທຶກລົງຕາຕະລາງກາງ
                mysqli_query($conn, "INSERT INTO tour_assigned_guides (tour_id, guide_id) VALUES ($tour_id, $gid)");
                
                // ໝາຍເຫດ: ຕອນນີ້ເຮົາບໍ່ຕ້ອງ UPDATE status = 'Busy' ແລ້ວ 
                // ເພາະລະບົບຈະຄຳນວນຈາກວັນທີໃນທົວທີ່ Active ໃຫ້ເອງ.
            }
        }
        header("Location: add.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>