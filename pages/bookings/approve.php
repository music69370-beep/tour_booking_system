<?php
include '../../config/db.php';

// 1. ກວດສອບສິດ: ຕ້ອງເປັນ Admin ເທົ່ານັ້ນ
if (!isAdmin()) {
    header("Location: index.php?msg=error_permission");
    exit();
}

/** @var mysqli $conn */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../../includes/PHPMailer/Exception.php';
require '../../includes/PHPMailer/PHPMailer.php';
require '../../includes/PHPMailer/SMTP.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

    // ດຶງ ID ແລະ ຊື່ Admin ທີ່ກຳລັງອະນຸມັດ
    $admin_id = $_SESSION['user_id'];
    $admin_name = $_SESSION['fullname'];

    // 2. ປັບ SQL ໃຫ້ດຶງຂໍ້ມູນຄົບຖ້ວນ (ລວມທັງລົດ ແລະ ຄົນຂັບ)
    $sql_info = "SELECT b.*, c.fullname, c.email, 
                       t.tour_name, t.meeting_point,
                       v.model as vehicle_model, v.plate_number,
                       d.fullname as driver_name, d.phone as driver_phone
                FROM bookings b 
                JOIN customers c ON b.customer_id = c.customer_id 
                JOIN tours t ON b.tour_id = t.tour_id 
                LEFT JOIN vehicle_outings vo ON (b.tour_id = vo.tour_id AND b.travel_date = vo.start_date)
                LEFT JOIN vehicles v ON vo.vehicle_id = v.vehicle_id
                LEFT JOIN drivers d ON vo.driver_id = d.driver_id
                WHERE b.booking_id = '$id'";
    
    $res_info = mysqli_query($conn, $sql_info);

    if ($res_info && mysqli_num_rows($res_info) > 0) {
        $data = mysqli_fetch_assoc($res_info);

        // 3. ອັບເດດສະຖານະການຈອງ
        if (mysqli_query($conn, "UPDATE bookings SET status = 'Confirmed', approved_by = '$admin_id' WHERE booking_id = '$id'")) {
            
            // 4. ສົ່ງ Email ຫາລູກຄ້າ ພ້ອມລາຍລະອຽດທີ່ຄົບຖ້ວນ
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; 
                $mail->SMTPAuth   = true;
                $mail->Username   = 'pern69370@gmail.com'; 
                $mail->Password   = 'jqwq rbex dxht zmcf';    
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom('pern69370@gmail.com', 'Tour Booking System');
                $mail->addAddress($data['email'], $data['fullname']);

                $mail->isHTML(true);
                $mail->Subject = 'ຢືນຢັນການຈອງທົວສຳເລັດແລ້ວ - BK-' . str_pad($id, 4, '0', STR_PAD_LEFT);
                
                // ເນື້ອໃນ Email ແບບລະອຽດ
                $mail->Body = "
                <div style='font-family: \"Noto Sans Lao\", sans-serif, Arial; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);'>
                    <div style='background-color: #198754; color: white; padding: 25px; text-align: center;'>
                        <h2 style='margin: 0;'>ການຈອງຂອງທ່ານໄດ້ຮັບການອະນຸມັດແລ້ວ!</h2>
                        <p style='margin: 5px 0 0; opacity: 0.9;'>Tour Confirmed & Ready to Go</p>
                    </div>
                    
                    <div style='padding: 30px; background-color: #ffffff;'>
                        <p>ສະບາຍດີ <b>".$data['fullname']."</b>,</p>
                        <p>ພວກເຮົາໄດ້ກວດສອບການຊຳລະເງິນ ແລະ ຢືນຢັນການຈອງຂອງທ່ານຮຽບຮ້ອຍແລ້ວ. ລາຍລະອຽດມີດັ່ງນີ້:</p>
                        
                        <!-- ສ່ວນຂໍ້ມູນການຈອງ -->
                        <div style='background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;'>
                            <p style='margin: 0 0 10px;'><b>ເລກທີການຈອງ:</b> #BK-".str_pad($id, 4, '0', STR_PAD_LEFT)."</p>
                            <p style='margin: 0 0 10px;'><b>ແພັກເກັດທົວ:</b> <span style='color: #0d6efd;'>".$data['tour_name']."</span></p>
                            <p style='margin: 0 0 10px;'><b>ວັນທີເດີນທາງ:</b> ".date('d/m/Y', strtotime($data['travel_date']))."</p>
                            <p style='margin: 0;'><b>ຈຸດນັດພົບ:</b> ".$data['meeting_point']."</p>
                        </div>

                        <!-- ສ່ວນຂໍ້ມູນລົດ ແລະ ຄົນຂັບ (Transportation) -->
                        <div style='border: 1px solid #ff9800; background-color: #fffdf0; padding: 20px; border-radius: 10px; margin-bottom: 20px;'>
                            <h4 style='margin: 0 0 15px; color: #e67e22; border-bottom: 1px solid #ff9800; padding-bottom: 5px;'>🚌 ຂໍ້ມູນການເດີນທາງ (Transportation)</h4>
                            <p style='margin: 0 0 10px;'><b>ລົດທົວ:</b> ".($data['vehicle_model'] ?: 'ຈະແຈ້ງໃຫ້ຊາບພາຍຫຼັງ')." (".($data['plate_number'] ?: '---').")</p>
                            <p style='margin: 0 0 10px;'><b>ຄົນຂັບ:</b> ".($data['driver_name'] ?: '---')."</p>
                            <p style='margin: 0;'><b>ເບີໂທຕິດຕໍ່ຄົນຂັບ:</b> <span style='color: #0d6efd;'>".($data['driver_phone'] ?: '---')."</span></p>
                        </div>

                        <div style='padding: 15px; border-left: 4px solid #198754; background-color: #f1f8f5; font-size: 13px;'>
                            <b>ພະນັກງານຜູ້ດູແລ (Handled by):</b><br>
                            $admin_name
                        </div>

                        <p style='margin-top: 25px; text-align: center; color: #666; font-size: 13px;'>ກະລຸນາສະແດງ Email ນີ້ ຫຼື ບັດ Voucher ໃນມື້ເດີນທາງ</p>
                    </div>

                    <div style='background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 11px; color: #999;'>
                        © 2026 Tour Booking System. All Rights Reserved.
                    </div>
                </div>";

                $mail->send();
            } catch (Exception $e) { }

            header("Location: index.php?msg=updated&status=$status_filter");
            exit();
        }
    }
}
?>