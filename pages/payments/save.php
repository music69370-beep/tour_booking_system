<?php
include '../../config/db.php';

if (isset($_POST['save_payment'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $payment_date = $_POST['payment_date'];

    // ດຶງຍອດເງິນ
    $res_price = mysqli_query($conn, "SELECT total_price FROM bookings WHERE booking_id = '$booking_id'");
    $price_data = mysqli_fetch_assoc($res_price);
    $amount = $price_data['total_price'];

    // ຈັດການຮູບໃບບິນ
    $file_name = $_FILES['payment_slip']['name'];
    $tmp_name = $_FILES['payment_slip']['tmp_name'];
    $new_file_name = time() . "_" . $file_name;
    $target = "../../assets/uploads/payments/" . $new_file_name;

    if (move_uploaded_file($tmp_name, $target)) {
        // ບັນທຶກການຈ່າຍເງິນ
        $sql_pay = "INSERT INTO payments (booking_id, amount, payment_method, payment_slip, payment_date) 
                    VALUES ('$booking_id', '$amount', '$payment_method', '$new_file_name', '$payment_date')";
        
        if (mysqli_query($conn, $sql_pay)) {
            // *** ຈຸດທີ່ແກ້ໄຂ: ເຮົາຈະບໍ່ UPDATE status ເປັນ Confirmed ຢູ່ບ່ອນນີ້ ***
            // ໃຫ້ສະຖານະການຈອງເປັນ Pending ໄວ້ຄືເກົ່າ ເພື່ອໃຫ້ແອດມິນມາກວດສອບເອງ
            
            if (isset($_POST['from_customer'])) {
                echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <body style='font-family: Arial;'>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'ສົ່ງຫຼັກຖານສຳເລັດ!',
                        text: 'ແອດມິນຈະກວດສອບຍອດເງິນ ແລະ ຢືນຢັນການຈອງໃຫ້ທ່ານໂດຍໄວ',
                        confirmButtonText: 'ຕົກລົງ'
                    }).then(() => { window.location.href = '../../index.php'; });
                </script>
                </body>";
            } else {
                header("Location: index.php?msg=success");
            }
        }
    }
}
?>