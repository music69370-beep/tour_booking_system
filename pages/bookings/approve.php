<?php
include '../../config/db.php';

// ບລັອກບໍ່ໃຫ້ Staff ເຂົ້າໜ້ານີ້
if (!isAdmin()) {
    header("Location: index.php?msg=error_permission");
    exit();
}

/** @var mysqli $conn */
// ... ໂຄ້ດເກົ່າຂອງເຈົ້າ ...

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

    // 1. ດຶງຂໍ້ມູນການຈອງ (ແກ້ໄຂ SQL: ຕັດການ JOIN ລົດ ແລະ ໄກ້ອອກ ເພາະ Column ໃນ tours ບໍ່ມີແລ້ວ)
    $sql_info = "SELECT b.*, c.fullname, c.email, 
                       t.tour_name, t.duration, t.meeting_point
                FROM bookings b 
                JOIN customers c ON b.customer_id = c.customer_id 
                JOIN tours t ON b.tour_id = t.tour_id 
                WHERE b.booking_id = '$id'";
    
    $res_info = mysqli_query($conn, $sql_info);

    // ກວດສອບຄວາມຖືກຕ້ອງຂອງຂໍ້ມູນ
    if ($res_info && mysqli_num_rows($res_info) > 0) {
        $data = mysqli_fetch_assoc($res_info);

        // 2. ອັບເດດສະຖານະເປັນ Confirmed
        if (mysqli_query($conn, "UPDATE bookings SET status = 'Confirmed' WHERE booking_id = '$id'")) {
            
            // 3. ສົ່ງ Email ຫາລູກຄ້າ
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; 
                $mail->SMTPAuth   = true;
                $mail->Username   = 'pern69370@gmail.com'; // Email ຂອງເຈົ້າ
                $mail->Password   = 'jqwq rbex dxht zmcf';    // App Password 16 ຫຼັກ
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                // ຕັ້ງຄ່າ SSL ສຳລັບ Localhost
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                $mail->setFrom('pern69370@gmail.com', 'Tour Booking System');
                $mail->addAddress($data['email'], $data['fullname']);

                // --- ເນື້ອໃນ Email ---
                $mail->isHTML(true);
                $mail->Subject = 'ຢືນຢັນການຈອງທົວສຳເລັດແລ້ວ - BK-' . str_pad($id, 4, '0', STR_PAD_LEFT);
                
                $mail->Body = "
                <div style='font-family: \"Noto Sans Lao\", Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e0e0e0; border-radius: 15px; overflow: hidden;'>
                    <div style='background-color: #0d6efd; padding: 30px; text-align: center; color: white;'>
                        <h1 style='margin: 0; font-size: 24px;'>ໃບຢັ້ງຢືນການຈອງທົວ</h1>
                        <p style='margin: 5px 0 0; opacity: 0.8;'>Tour Booking Confirmation</p>
                    </div>
                    
                    <div style='padding: 30px; background-color: #ffffff;'>
                        <h2 style='color: #333;'>ສະບາຍດີ, ".$data['fullname']."!</h2>
                        <p style='color: #666; line-height: 1.6;'>ພວກເຮົາໄດ້ຮັບການຊຳລະເງິນ ແລະ ຢືນຢັນການຈອງຂອງທ່ານຮຽບຮ້ອຍແລ້ວ. ລາຍລະອຽດມີດັ່ງນີ້:</p>
                        
                        <div style='background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 8px 0; color: #777;'>ເລກທີການຈອງ:</td>
                                    <td style='padding: 8px 0; font-weight: bold; text-align: right;'>#BK-".str_pad($id, 4, '0', STR_PAD_LEFT)."</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #777;'>ແພັກເກັດ:</td>
                                    <td style='padding: 8px 0; font-weight: bold; text-align: right; color: #0d6efd;'>".$data['tour_name']."</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #777;'>ວັນທີເດີນທາງ:</td>
                                    <td style='padding: 8px 0; font-weight: bold; text-align: right; color: #ff4757;'>".date('d/m/Y', strtotime($data['travel_date']))."</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #777;'>ຈຸດນັດພົບ:</td>
                                    <td style='padding: 8px 0; font-weight: bold; text-align: right;'>".$data['meeting_point']."</td>
                                </tr>
                            </table>
                        </div>

                        <div style='text-align: center; margin-top: 30px;'>
                            <p style='font-size: 13px; color: #888;'>ກະລຸນາສະແດງ Email ນີ້ໃຫ້ເຈົ້າໜ້າທີ່ໃນມື້ເດີນທາງ</p>
                            <div style='padding: 10px 20px; border: 2px dashed #0d6efd; border-radius: 10px; display: inline-block;'>
                                <span style='font-weight: bold; color: #0d6efd;'>STATUS: CONFIRMED</span>
                            </div>
                        </div>
                    </div>

                    <div style='background-color: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #999;'>
                        <p style='margin: 0;'>© 2026 Tour Booking System. All Rights Reserved.</p>
                    </div>
                </div>";

                $mail->send();
            } catch (Exception $e) {
                // ຖ້າສົ່ງ Email ບໍ່ໄດ້ ກໍໃຫ້ຂ້າມໄປ (ລະບົບຍັງອັບເດດສະຖານະໃຫ້ແລ້ວ)
            }

            header("Location: index.php?msg=updated&status=$status_filter");
            exit();
        }
    } else {
        echo "Error: Could not fetch booking data. " . mysqli_error($conn);
    }
}
?>