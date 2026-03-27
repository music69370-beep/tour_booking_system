<?php
include '../../config/db.php';

// --- ນຳເຂົ້າ PHPMailer ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../../includes/PHPMailer/Exception.php';
require '../../includes/PHPMailer/PHPMailer.php';
require '../../includes/PHPMailer/SMTP.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

    // 1. ດຶງຂໍ້ມູນການຈອງ ແລະ Email ລູກຄ້າກ່ອນຈະອັບເດດ
    $sql_info = "SELECT b.*, c.fullname, c.email, t.tour_name, t.duration 
                 FROM bookings b 
                 JOIN customers c ON b.customer_id = c.customer_id 
                 JOIN tours t ON b.tour_id = t.tour_id 
                 WHERE b.booking_id = '$id'";
    $res_info = mysqli_query($conn, $sql_info);
    $data = mysqli_fetch_assoc($res_info);

    if ($data) {
        // 2. ອັບເດດສະຖານະເປັນ Confirmed
        if (mysqli_query($conn, "UPDATE bookings SET status = 'Confirmed' WHERE booking_id = '$id'")) {
            
            // 3. ສົ່ງ Email ຫາລູກຄ້າ
            $mail = new PHPMailer(true);

            try {
                // ເຊັດ SMTP (ຕົວຢ່າງໃຊ້ Gmail)
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; 
                $mail->SMTPAuth   = true;
                $mail->Username   = 'YOUR_EMAIL@gmail.com'; // Email ຂອງເຈົ້າ
                $mail->Password   = 'jqwq rbex dxht zmcf';    // App Password ຈາກ Google
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->setFrom('YOUR_EMAIL@gmail.com', 'Tour Booking System');
                $mail->addAddress($data['email'], $data['fullname']);
                $mail->CharSet = 'UTF-8';

                // ເນື້ອໃນ Email
                $mail->isHTML(true);
                $mail->Subject = 'ຢືນຢັນການຈອງທົວສຳເລັດແລ້ວ - BK-' . $id;
                $mail->Body    = "
                    <div style='font-family: sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                        <h2 style='color: #0d6efd;'>ສະບາຍດີ, ".$data['fullname']."!</h2>
                        <p>ການຈອງທົວຂອງທ່ານໄດ້ຮັບການຢືນຢັນຮຽບຮ້ອຍແລ້ວ.</p>
                        <hr>
                        <p><strong>ລາຍການ:</strong> ".$data['tour_name']."</p>
                        <p><strong>ໄລຍະເວລາ:</strong> ".$data['duration']."</p>
                        <p><strong>ວັນທີເດີນທາງ:</strong> ".date('d/m/Y', strtotime($data['travel_date']))."</p>
                        <p><strong>ຈຳນວນ:</strong> ".$data['num_people']." ຄົນ</p>
                        <p><strong>ລາຄາລວມ:</strong> ".number_format($data['total_price'])." ກີບ</p>
                        <hr>
                        <p style='color: #198754; font-weight: bold;'>ກະລຸນານຳເອົາ Email ສະບັບນີ້ໄປຢັ້ງຢືນໃນມື້ເດີນທາງ.</p>
                        <p>ຂອບໃຈທີ່ໃຊ້ບໍລິການກັບພວກເຮົາ!</p>
                    </div>";

                $mail->send();
            } catch (Exception $e) {
                // ຖ້າສົ່ງບໍ່ໄດ້ ກໍບໍ່ເປັນຫຍັງ ໃຫ້ລະບົບທຳງານຕໍ່
            }

            header("Location: index.php?msg=updated&status=$status_filter");
            exit();
        }
    }
}
?>