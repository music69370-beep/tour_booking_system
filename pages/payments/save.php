<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_payment'])) {
    $booking_id     = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $payment_date   = $_POST['payment_date'];
    
    // ເກັບ ID ຜູ້ບັນທຶກເງິນ (ຖ້າມີການ Login)
    $received_by = isset($_SESSION['user_id']) ? "'".$_SESSION['user_id']."'" : "NULL";

    // ຈັດການເລື່ອງຈຳນວນເງິນ
    if(isset($_POST['amount']) && !empty($_POST['amount'])) { 
        $amount = $_POST['amount']; 
    } else {
        // ຖ້າບໍ່ໄດ້ສົ່ງຍອດເງິນມາ ໃຫ້ໄປດຶງຍອດລວມຈາກການຈອງ
        $res_p = mysqli_query($conn, "SELECT total_price FROM bookings WHERE booking_id = '$booking_id'");
        $data_p = mysqli_fetch_assoc($res_p);
        $amount = $data_p['total_price'];
    }

    // ຈັດການອັບໂຫລດຮູບພາບສະລິບ
    $new_file_name = "";
    if (isset($_FILES['payment_slip']) && !empty($_FILES['payment_slip']['name'])) {
        $upload = save_uploaded_image($_FILES['payment_slip'], "../../assets/uploads/payments/");
        $new_file_name = $upload;
    }

    // 1. ບັນທຶກຂໍ້ມູນການຊຳລະເງິນລົງໃນຕາຕະລາງ payments
    $sql = "INSERT INTO payments (booking_id, amount, payment_method, payment_slip, payment_date, received_by) 
            VALUES ('$booking_id', '$amount', '$payment_method', '$new_file_name', '$payment_date', $received_by)";
    
    if (mysqli_query($conn, $sql)) {
        
        /* 
           ໝາຍເຫດ: ຕັດສ່ວນ UPDATE bookings SET status = 'Confirmed' ອອກແລ້ວ 
           ເພື່ອບໍ່ໃຫ້ລະບົບອະນຸມັດເອງອັດຕະໂນມັດ. 
           ສະຖານະຈະເປັນ 'Pending' ຄືເກົ່າ ຈົນກວ່າ Admin ຈະກົດອະນຸມັດຢູ່ໜ້າລາຍການຈອງ.
        */

        // ກຳນົດ URL ທີ່ຈະກັບໄປ (ລູກຄ້າໄປໜ້າຫຼັກ / Admin ໄປໜ້າລາຍການຈອງ)
        $target_url = (isset($_POST['from_customer']) && $_POST['from_customer'] == '1') ? "../../index.php" : "../bookings/index.php";

        // ສະແດງຂໍ້ຄວາມແຈ້ງເຕືອນດ້ວຍ SweetAlert
        echo "
        <!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
                body { font-family: 'Noto Sans Lao', sans-serif; }
            </style>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'ບັນທຶກການຊຳລະເງິນສຳເລັດ!',
                    text: 'ກະລຸນາລໍຖ້າເຈົ້າໜ້າທີ່ກວດສອບ ແລະ ຢືນຢັນການຈອງ',
                    confirmButtonText: 'ຕົກລົງ',
                    confirmButtonColor: '#0d6efd'
                }).then(() => {
                    window.location.href = '$target_url';
                });
            </script>
        </body>
        </html>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>