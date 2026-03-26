<?php
include '../../config/db.php';

if (isset($_POST['save_booking'])) {
    $customer_id = $_POST['customer_id'];
    $tour_id = $_POST['tour_id'];
    $num_people = $_POST['num_people'];
    $total_price = $_POST['total_price'];
    $status = "Pending"; // ເລີ່ມຕົ້ນເປັນ Pending (ລໍຖ້າການຢືນຢັນ)

    $sql = "INSERT INTO bookings (customer_id, tour_id, num_people, total_price, status) 
            VALUES ('$customer_id', '$tour_id', '$num_people', '$total_price', '$status')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>