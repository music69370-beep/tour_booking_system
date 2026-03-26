<?php
include '../../config/db.php';

if (isset($_POST['save_booking'])) {
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $num_people = intval($_POST['num_people']);
    $total_price = mysqli_real_escape_string($conn, $_POST['total_price']);

    // 1. ກວດສອບບ່ອນນັ່ງສູງສຸດ
    $tour_query = mysqli_query($conn, "SELECT max_seats FROM tours WHERE tour_id = $tour_id");
    
    // ເພີ່ມການກວດສອບ Error ບ່ອນນີ້
    if (!$tour_query) {
        die("Error fetching tour data: " . mysqli_error($conn));
    }
    
    $tour_data = mysqli_fetch_assoc($tour_query);
    $max_seats = isset($tour_data['max_seats']) ? $tour_data['max_seats'] : 0;

    // 2. ນັບຈຳນວນຄົນທີ່ຈອງໄປແລ້ວ
    $booked_query = mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tour_id AND status != 'Cancelled'");
    $booked_data = mysqli_fetch_assoc($booked_query);
    $already_booked = $booked_data['total'] ?? 0;

    $remaining_seats = $max_seats - $already_booked;

    // 3. ເຊັກບ່ອນນັ່ງ
    if ($num_people > $remaining_seats) {
        echo "<script>alert('ຂໍອະໄພ! ບ່ອນນັ່ງບໍ່ພໍ (ເຫຼືອພຽງ $remaining_seats ບ່ອນ)'); window.history.back();</script>";
        exit();
    }

    // 4. ບັນທຶກ
    $sql = "INSERT INTO bookings (customer_id, tour_id, num_people, total_price, status) 
            VALUES ('$customer_id', '$tour_id', '$num_people', '$total_price', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>