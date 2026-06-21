<?php
// 1. ເປີດການສະແດງ Error ເພື່ອຫາສາເຫດ (Debug Mode)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../config/db.php';
/** @var mysqli $conn */

// ກວດສອບວ່າມີການສົ່ງຂໍ້ມູນມາແທ້ບໍ່
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['customer_id'])) {
    
    // ຮັບຄ່າ ແລະ ປ້ອງກັນ SQL Injection
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $num_people = intval($_POST['num_people']);
    $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
    $selected_seats = mysqli_real_escape_string($conn, $_POST['selected_seats']);
    $total_price = mysqli_real_escape_string($conn, $_POST['total_price']);
    
    $single_fee = ($room_type == 'Single') ? 200000 : 0;

    // --- ປ້ອງກັນການບັນທຶກຊ້ຳ (Double Submit Protection) ---
    // ແກ້ໄຂຈາກ created_at ເປັນ booking_date ໃຫ້ຕົງກັບ DB ຂອງເຈົ້າ
    $check_duplicate = mysqli_query($conn, "SELECT booking_id FROM bookings 
        WHERE customer_id = '$customer_id' 
        AND tour_id = '$tour_id' 
        AND travel_date = '$travel_date' 
        AND selected_seats = '$selected_seats' 
        AND booking_date > (NOW() - INTERVAL 5 SECOND)");

    if ($check_duplicate && mysqli_num_rows($check_duplicate) > 0) {
        header("Location: index.php?msg=success");
        exit();
    }

    // 2. ບັນທຶກການຈອງຫຼັກລົງຕາຕະລາງ bookings
    $sql_book = "INSERT INTO bookings (customer_id, tour_id, travel_date, num_people, room_type, total_price, single_supplement_fee, selected_seats, status) 
                 VALUES ('$customer_id', '$tour_id', '$travel_date', $num_people, '$room_type', '$total_price', '$single_fee', '$selected_seats', 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        $booking_id = mysqli_insert_id($conn);

        // 3. ບັນທຶກລາຍຊື່ຜູ້ຮ່ວມທາງ
        if (isset($_POST['participant_names']) && is_array($_POST['participant_names'])) {
            $p_names = $_POST['participant_names'];
            $p_cards = $_POST['participant_id_cards'] ?? []; 
            
            foreach ($p_names as $index => $name) {
                $clean_name = mysqli_real_escape_string($conn, trim($name));
                $clean_card = isset($p_cards[$index]) ? mysqli_real_escape_string($conn, trim($p_cards[$index])) : '';
                
                if (!empty($clean_name)) {
                    // ດຶງຂໍ້ມູນລູກຄ້າຫຼັກ
                    $c_res = mysqli_query($conn, "SELECT fullname FROM customers WHERE customer_id = '$customer_id'");
                    $c_data = mysqli_fetch_assoc($c_res);
                    $note = "Traveling with " . ($c_data['fullname'] ?? 'Lead Customer');

                    mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_id_card, participant_phone) 
                                         VALUES ('$booking_id', '$clean_name', '$clean_card', '$note')");
                }
            }
        }

        // ສົ່ງກັບໄປໜ້າລາຍການຈອງ
        header("Location: index.php?msg=success");
        exit();
        
    } else {
        // ໂຊ Error ຖ້າບັນທຶກບໍ່ໄດ້
        die("Error Saving Booking: " . mysqli_error($conn));
    }
} else {
    header("Location: index.php");
    exit();
}
?>