<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../../config/db.php';
/** @var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_booking'])) {
    
    // ດຶງ ID ພະນັກງານຈາກ Session
    $user_id = $_SESSION['user_id']; 

    $customer_id    = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $tour_id        = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date    = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $num_people     = intval($_POST['num_people']);
    $room_type      = mysqli_real_escape_string($conn, $_POST['room_type']);
    $selected_seats = mysqli_real_escape_string($conn, $_POST['selected_seats']);
    $total_price    = mysqli_real_escape_string($conn, $_POST['total_price']);
    
    $single_fee = ($room_type == 'Single') ? 200000 : 0;

    // ເພີ່ມ Column 'created_by' ໃນການ INSERT
    $sql_book = "INSERT INTO bookings (customer_id, tour_id, travel_date, num_people, room_type, total_price, single_supplement_fee, selected_seats, status, created_by) 
                 VALUES ('$customer_id', '$tour_id', '$travel_date', $num_people, '$room_type', '$total_price', '$single_fee', '$selected_seats', 'Pending', '$user_id')";

    if (mysqli_query($conn, $sql_book)) {
        $booking_id = mysqli_insert_id($conn);

        if (isset($_POST['participant_names']) && is_array($_POST['participant_names'])) {
            $p_names  = $_POST['participant_names'];
            $p_phones = $_POST['participant_phones'] ?? [];

            foreach ($p_names as $index => $name) {
                $clean_name  = mysqli_real_escape_string($conn, trim($name));
                $clean_phone = isset($p_phones[$index]) ? mysqli_real_escape_string($conn, trim($p_phones[$index])) : '';
                
                if (!empty($clean_name)) {
                    $sql_part = "INSERT INTO booking_participants (booking_id, participant_name, participant_phone) 
                                VALUES ('$booking_id', '$clean_name', '$clean_phone')";
                    mysqli_query($conn, $sql_part);
                }
            }
        }

        header("Location: index.php?msg=success");
        exit();
    } else {
        die("ເກີດຂໍ້ຜິດພາດໃນການ SQL: " . mysqli_error($conn));
    }
}