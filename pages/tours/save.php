<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_tour'])) {
    // ຮັບຄ່າ ແລະ ເຊັກວ່າວ່າງຫຼືບໍ່ (ຖ້າວ່າງໃຫ້ເປັນຄ່າ Default)
    $tour_code = mysqli_real_escape_string($conn, $_POST['tour_code']);
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $category  = mysqli_real_escape_string($conn, $_POST['category']);
    $price     = $_POST['price'];
    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];
    
    // ເຊັກຄ່າ duration ແລະ meals ປ້ອງກັນ Warning
    $duration  = isset($_POST['duration']) ? mysqli_real_escape_string($conn, $_POST['duration']) : '';
    $meals     = isset($_POST['meals']) ? intval($_POST['meals']) : 0;
    
    $max_seats = intval($_POST['max_seats']);
    $meeting_point = mysqli_real_escape_string($conn, $_POST['meeting_point'] ?? '');
    $highlights    = mysqli_real_escape_string($conn, $_POST['highlights'] ?? '');
    $whats_included = mysqli_real_escape_string($conn, $_POST['whats_included'] ?? '');
    $whats_excluded = mysqli_real_escape_string($conn, $_POST['whats_excluded'] ?? '');
    $itinerary      = mysqli_real_escape_string($conn, $_POST['itinerary'] ?? '[]');
    
    // ຮັບຄ່າ guide_id
    $guide_id = !empty($_POST['guide_id']) ? intval($_POST['guide_id']) : "NULL";

    // ຈັດການຮູບພາບ
    $image_name = "";
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = "../../assets/uploads/tours/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
    }

    // ບັນທຶກ (ກວດສອບວ່າ column 'guide_id' ມີແລ້ວໃນ DB ຕາມຂັ້ນຕອນທີ 1)
    $sql = "INSERT INTO tours (
                tour_code, tour_name, guide_id, category, price, start_date, end_date, 
                duration, max_seats, meals, meeting_point, highlights, 
                whats_included, whats_excluded, itinerary, image, status
            ) VALUES (
                '$tour_code', '$tour_name', $guide_id, '$category', '$price', '$start_date', '$end_date', 
                '$duration', $max_seats, $meals, '$meeting_point', '$highlights', 
                '$whats_included', '$whats_excluded', '$itinerary', '$image_name', 'Active'
            )";

    if (mysqli_query($conn, $sql)) {
        $tour_id = mysqli_insert_id($conn);
        
        // ອັບເດດສະຖານະໄກ້
        if ($guide_id !== "NULL") {
            mysqli_query($conn, "UPDATE guides SET status = 'Busy' WHERE guide_id = $guide_id");
        }

        header("Location: add.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>