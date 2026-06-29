<?php
include '../../config/db.php';
/** @var mysqli $conn */
if (isset($_POST['save_payment'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $payment_date = $_POST['payment_date'];
    
    // ເຊັກວ່າຍອດເງິນມາຈາກຟອມ (Admin) ຫຼື ຕ້ອງດຶງຈາກ DB (Customer)
    if(isset($_POST['amount'])) {
        $amount = $_POST['amount'];
    } else {
        $res_price = mysqli_query($conn, "SELECT total_price FROM bookings WHERE booking_id = '$booking_id'");
        $price_data = mysqli_fetch_assoc($res_price);
        $amount = $price_data['total_price'];
    }

    // ຈັດການຮູບໃບບິນ
    $new_file_name = "";
    if (!empty($_FILES['payment_slip']['name'])) {
        $file_name = $_FILES['payment_slip']['name'];
        $tmp_name = $_FILES['payment_slip']['tmp_name'];
        $new_file_name = time() . "_" . $file_name;
        
        // ກວດສອບ ແລະ ສ້າງໂຟເດີຖ້າຍັງບໍ່ມີ
        if (!is_dir("../../assets/uploads/payments/")) {
            mkdir("../../assets/uploads/payments/", 0777, true);
        }
        move_uploaded_file($tmp_name, "../../assets/uploads/payments/" . $new_file_name);
    }

    // 1. ບັນທຶກຂໍ້ມູນລົງຕາຕະລາງ payments
    $sql_pay = "INSERT INTO payments (booking_id, amount, payment_method, payment_slip, payment_date) 
                VALUES ('$booking_id', '$amount', '$payment_method', '$new_file_name', '$payment_date')";
    
    if (mysqli_query($conn, $sql_pay)) {
        
        // 2. ເຊັກວ່າແມ່ນ "ລູກຄ້າ" ສົ່ງມາ ຫຼື "ແອັດມິນ" ບັນທຶກເອງ
        if (isset($_POST['from_customer']) && $_POST['from_customer'] == '1') {
            
            // --- ກໍລະນີລູກຄ້າ: ຫ້າມ UPDATE STATUS (ໃຫ້ເປັນ Pending ຄືເກົ່າ) ---
            echo "
            <!DOCTYPE html>
            <html>
            <head>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <style>@import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap'); body { font-family: 'Noto Sans Lao', sans-serif; }</style>
            </head>
            <body>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'ສົ່ງຫຼັກຖານສຳເລັດ!',
                        text: 'ແອັດມິນຈະກວດສອບສະລິບ ແລະ ຢືນຢັນການຈອງໃຫ້ທ່ານພາຍໃນ 24 ຊົ່ວໂມງ',
                        confirmButtonText: 'ຕົກລົງ',
                        confirmButtonColor: '#0d6efd'
                    }).then(() => {
                        window.location.href = '../../index.php'; // ກັບໄປໜ້າຫຼັກຂອງລູກຄ້າ
                    });
                </script>
            </body>
            </html>";
            exit();

        } else {
            
            // --- ກໍລະນີແອັດມິນ: ໃຫ້ອັບເດດເປັນ Confirmed ທັນທີ (ເພາະແອັດມິນກວດເງິນແລ້ວຈຶ່ງປ້ອນ) ---
            mysqli_query($conn, "UPDATE bookings SET status = 'Confirmed' WHERE booking_id = '$booking_id'");
            header("Location: index.php?msg=success");
            exit();
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>