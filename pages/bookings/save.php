<?php
include '../../config/db.php';

if (isset($_POST['save_booking'])) {
    $customer_id = $_POST['customer_id'];
    $tour_id = $_POST['tour_id'];
    $travel_date = $_POST['travel_date']; // ຮັບຄ່າໃໝ່
    $num_people = intval($_POST['num_people']);
    $total_price = $_POST['total_price'];

    $sql = "INSERT INTO bookings (customer_id, tour_id, travel_date, num_people, total_price, status) 
            VALUES ('$customer_id', '$tour_id', '$travel_date', '$num_people', '$total_price', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        $booking_id = mysqli_insert_id($conn);
        if (isset($_POST['participant_names'])) {
            $names = $_POST['participant_names'];
            $phones = $_POST['participant_phones'];
            for ($i = 0; $i < count($names); $i++) {
                $p_name = mysqli_real_escape_string($conn, $names[$i]);
                $p_phone = mysqli_real_escape_string($conn, $phones[$i]);
                mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_phone) VALUES ($booking_id, '$p_name', '$p_phone')");
            }
        }
        header("Location: index.php?msg=success");
    }
}
?>