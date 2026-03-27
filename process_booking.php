<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $num_people = intval($_POST['num_people']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    // --- ຈຸດສຳຄັນ: ດຶງລາຄາຈາກ DB ມາຄຳນວນເອງ ເພື່ອຄວາມປອດໄພ ແລະ ແກ້ Error ---
    $tour_res = mysqli_query($conn, "SELECT price FROM tours WHERE tour_id = '$tour_id'");
    $tour_data = mysqli_fetch_assoc($tour_res);
    $price_per_person = $tour_data['price'];
    $total_price = $price_per_person * $num_people;

    // 1. ບັນທຶກລູກຄ້າ
    mysqli_query($conn, "INSERT INTO customers (fullname, phone, email) VALUES ('$fullname', '$phone', '$email')");
    $customer_id = mysqli_insert_id($conn);

    // 2. ບັນທຶກການຈອງ
    $sql_book = "INSERT INTO bookings (customer_id, tour_id, travel_date, num_people, total_price, status) 
                 VALUES ($customer_id, $tour_id, '$travel_date', $num_people, $total_price, 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        $booking_id = mysqli_insert_id($conn);

        // 3. ບັນທຶກຜູ້ຮ່ວມທາງ
        if (isset($_POST['participant_names'])) {
            foreach ($_POST['participant_names'] as $index => $p_name) {
                $name = mysqli_real_escape_string($conn, $p_name);
                $p_phone = mysqli_real_escape_string($conn, $_POST['participant_phones'][$index]);
                mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_phone) VALUES ($booking_id, '$name', '$p_phone')");
            }
        }

        // ໄປໜ້າ Checkout ເພື່ອຊຳລະເງິນ
        header("Location: checkout.php?booking_id=$booking_id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>