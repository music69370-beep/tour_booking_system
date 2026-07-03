<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_payment'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $payment_date = $_POST['payment_date'];
    
    if(isset($_POST['amount'])) {
        $amount = $_POST['amount'];
    } else {
        $res_p = mysqli_query($conn, "SELECT total_price FROM bookings WHERE booking_id = '$booking_id'");
        $data_p = mysqli_fetch_assoc($res_p);
        $amount = $data_p['total_price'];
    }

    $new_file_name = "";
    if (isset($_FILES['payment_slip'])) {
        $upload = save_uploaded_image($_FILES['payment_slip'], "../../assets/uploads/payments/");
        $new_file_name = $upload;
    }

    $sql = "INSERT INTO payments (booking_id, amount, payment_method, payment_slip, payment_date) 
            VALUES ('$booking_id', '$amount', '$payment_method', '$new_file_name', '$payment_date')";
    
    if (mysqli_query($conn, $sql)) {
        
        // Redirect ຕາມສິດ
        if (isset($_POST['from_customer']) && $_POST['from_customer'] == '1') {
            $target_url = "../../index.php"; 
        } else {
            $target_url = "../bookings/add.php"; 
        }

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
                    text: 'ພວກເຮົາຈະກວດສອບ ແລະ ຢືນຢັນການຈອງໃຫ້ທ່ານໂດຍໄວ',
                    confirmButtonText: 'ຕົກລົງ',
                    confirmButtonColor: '#0d6efd'
                }).then(() => {
                    window.location.href = '$target_url';
                });
            </script>
        </body>
        </html>";
        exit();
    }
}
?>