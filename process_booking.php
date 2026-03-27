<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tour_id = $_POST['tour_id'];
    $travel_date = $_POST['travel_date'];
    $num_people = intval($_POST['num_people']);
    $coupon_id = !empty($_POST['coupon_id']) ? $_POST['coupon_id'] : 'NULL';
    $discount_amount = $_POST['discount_val'] ?: 0;

    $tour_res = mysqli_query($conn, "SELECT price FROM tours WHERE tour_id = '$tour_id'");
    $tour_data = mysqli_fetch_assoc($tour_res);
    $total_price = ($tour_data['price'] * $num_people) - $discount_amount;

    mysqli_query($conn, "INSERT INTO customers (fullname, phone, email) VALUES ('$fullname', '$phone', '$email')");
    $customer_id = mysqli_insert_id($conn);

    $sql_book = "INSERT INTO bookings (customer_id, tour_id, coupon_id, travel_date, num_people, total_price, discount_amount, status) 
                 VALUES ($customer_id, $tour_id, $coupon_id, '$travel_date', $num_people, $total_price, $discount_amount, 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        header("Location: checkout.php?booking_id=" . mysqli_insert_id($conn));
    }
}
?>