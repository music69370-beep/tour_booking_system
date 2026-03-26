<?php
include '../../config/db.php';

if (isset($_POST['save_booking'])) {
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $num_people = intval($_POST['num_people']);
    $total_price = mysqli_real_escape_string($conn, $_POST['total_price']);

    // 1. ບັນທຶກການຈອງ
    $sql = "INSERT INTO bookings (customer_id, tour_id, num_people, total_price, status) 
            VALUES ('$customer_id', '$tour_id', '$num_people', '$total_price', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        $booking_id = mysqli_insert_id($conn);

        // 2. ບັນທຶກລາຍຊື່ ແລະ ເບີໂທ ຜູ້ຮ່ວມທາງ (ຖ້າມີ)
        if (isset($_POST['participant_names']) && is_array($_POST['participant_names'])) {
            $names = $_POST['participant_names'];
            $phones = $_POST['participant_phones'];

            for ($i = 0; $i < count($names); $i++) {
                $p_name = mysqli_real_escape_string($conn, $names[$i]);
                $p_phone = mysqli_real_escape_string($conn, $phones[$i]);
                
                if (!empty($p_name)) {
                    mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_phone) 
                                         VALUES ($booking_id, '$p_name', '$p_phone')");
                }
            }
        }

        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>