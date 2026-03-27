<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $num_people = intval($_POST['num_people']);
    $total_price = mysqli_real_escape_string($conn, $_POST['total_price']);

    // 1. ບັນທຶກລູກຄ້າ
    mysqli_query($conn, "INSERT INTO customers (fullname, phone) VALUES ('$fullname', '$phone')");
    $customer_id = mysqli_insert_id($conn);

    // 2. ບັນທຶກການຈອງ
    $sql_book = "INSERT INTO bookings (customer_id, tour_id, num_people, total_price, status) 
                 VALUES ($customer_id, $tour_id, $num_people, $total_price, 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        $booking_id = mysqli_insert_id($conn);

        // 3. ບັນທຶກລາຍຊື່ຜູ້ຮ່ວມທາງ (ຖ້າມີ)
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

        // --- ຈຸດທີ່ແກ້ໄຂ: Redirect ໄປໜ້າ Checkout ພ້ອມສົ່ງ ID ໄປນຳ ---
        header("Location: checkout.php?booking_id=$booking_id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>