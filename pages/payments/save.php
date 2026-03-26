<?php
include '../../config/db.php';

if (isset($_POST['save_payment'])) {
    $booking_id = $_POST['booking_id'];
    $payment_method = $_POST['payment_method'];
    $payment_date = $_POST['payment_date'];

    // 1. ດຶງຍອດເງິນຈາກການຈອງມາບັນທຶກ
    $res_price = mysqli_query($conn, "SELECT total_price FROM bookings WHERE booking_id = $booking_id");
    $price_data = mysqli_fetch_assoc($res_price);
    $amount = $price_data['total_price'];

    // 2. ຈັດການຮູບໃບບິນ
    $file_name = $_FILES['payment_slip']['name'];
    $tmp_name = $_FILES['payment_slip']['tmp_name'];
    $new_file_name = time() . "_" . $file_name;
    $target = "../../assets/uploads/payments/" . $new_file_name;

    if (move_uploaded_file($tmp_name, $target)) {
        // 3. ບັນທຶກລົງ Table payments
        $sql_pay = "INSERT INTO payments (booking_id, amount, payment_method, payment_slip, payment_date) 
                    VALUES ('$booking_id', '$amount', '$payment_method', '$new_file_name', '$payment_date')";
        
        if (mysqli_query($conn, $sql_pay)) {
            // 4. ອັບເດດສະຖານະການຈອງໃຫ້ເປັນ Confirmed
            mysqli_query($conn, "UPDATE bookings SET status = 'Confirmed' WHERE booking_id = $booking_id");
            
            header("Location: index.php?msg=success");
        }
    } else {
        echo "Error uploading slip";
    }
}
?>