<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $num_people = intval($_POST['num_people']);
    $selected_seats = mysqli_real_escape_string($conn, $_POST['selected_seats']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    
    $tour_res = mysqli_query($conn, "SELECT price FROM tours WHERE tour_id = '$tour_id'");
    $tour_data = mysqli_fetch_assoc($tour_res);
    $total_price = $tour_data['price'] * $num_people;

    // 1. ບັນທຶກ ຫຼື ອັບເດດລູກຄ້າ
    mysqli_query($conn, "INSERT INTO customers (fullname, phone, email) VALUES ('$fullname', '$phone', '$email') ON DUPLICATE KEY UPDATE fullname='$fullname'");
    $customer_id = mysqli_insert_id($conn) ?: mysqli_fetch_assoc(mysqli_query($conn, "SELECT customer_id FROM customers WHERE phone='$phone'"))['customer_id'];

    // 2. ບັນທຶກການຈອງ (ເພີ່ມ selected_seats)
    $sql_book = "INSERT INTO bookings (customer_id, tour_id, travel_date, num_people, selected_seats, total_price, note, status) 
                 VALUES ($customer_id, $tour_id, '$travel_date', $num_people, '$selected_seats', $total_price, '$note', 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        header("Location: checkout.php?booking_id=" . mysqli_insert_id($conn));
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>