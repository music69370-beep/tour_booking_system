<?php 
include 'config/db.php'; 
/** @var mysqli $conn */
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

$sql = "SELECT b.*, c.fullname, c.phone, c.email, t.tour_name, t.tour_code, t.duration, t.meeting_point, 
               t.image, t.itinerary, t.whats_included
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        WHERE b.booking_id = '$id' AND b.status = 'Confirmed'";
$row = mysqli_fetch_assoc(mysqli_query($conn, $sql));
if (!$row) { echo "<script>alert('ບໍ່ພົບຂໍ້ມູນ'); window.location='index.php';</script>"; exit; }
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>Voucher #BK-<?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fa; }
        .ticket-container { max-width: 850px; margin: 30px auto; background: white; border-radius: 30px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1); }
        .ticket-header { background: #0d6efd; color: white; padding: 40px; text-align: center; }
        .info-label { font-size: 0.75rem; text-transform: uppercase; color: #888; font-weight: 700; }
        .info-value { font-weight: 700; color: #2d3436; font-size: 1.05rem; }
    </style>
</head>
<body>
<div class="container no-print text-center mt-4"><button onclick="window.print()" class="btn btn-primary rounded-pill px-5 shadow"><i class="fas fa-print me-2"></i> ພິມ Voucher</button></div>

<div class="ticket-container">
    <div class="ticket-header">
        <h2 class="fw-bold mb-1">ໃບຢັ້ງຢືນການຈອງທົວ</h2>
        <p class="mb-0 opacity-75">Booking ID: #BK-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></p>
    </div>
    
    <div class="p-4 p-md-5">
        <div class="row g-4 mb-5">
            <div class="col-6"><div class="info-label">ຊື່ຜູ້ເດີນທາງ</div><div class="info-value"><?php echo $row['fullname']; ?></div></div>
            <div class="col-6 text-end"><div class="info-label">ປະເພດຫ້ອງ</div><div class="badge bg-success px-3 py-2"><?php echo $row['room_type']; ?> Sharing</div></div>
        </div>

        <!-- Rooming List (ເພີ່ມໃໝ່) -->
        <div class="p-4 rounded-4 bg-light border mb-5">
            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-key me-2"></i>ຂໍ້ມູນການພັກເຊົາ (Rooming List)</h6>
            <?php 
            $ra_res = mysqli_query($conn, "SELECT * FROM booking_room_assignments WHERE booking_id = '$id' ORDER BY hotel_name");
            $curr_h = "";
            while($ra = mysqli_fetch_assoc($ra_res)):
                if($curr_h != $ra['hotel_name']): $curr_h = $ra['hotel_name'];
                    echo "<div class='fw-bold text-dark mt-3 small border-start border-4 border-primary ps-2 mb-2'>$curr_h</div>";
                endif; ?>
                <div class="d-flex justify-content-between small border-bottom py-2">
                    <span><?php echo $ra['participant_name']; ?></span>
                    <span class="fw-bold text-danger">ຫ້ອງ: <?php echo $ra['room_number'] ?: 'ລໍຖ້າຈັດສັນ'; ?></span>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="row align-items-center mt-5 pt-4 border-top">
            <div class="col-md-8">
                <h3 class="fw-bold text-success mb-1">ຊຳລະແລ້ວ: <?php echo number_format($row['total_price']); ?> ກີບ</h3>
                <p class="text-muted small mb-0">ກະລຸນາສະແດງໃບຢັ້ງຢືນນີ້ໃນມື້ເດີນທາງ</p>
            </div>
            <div class="col-md-4 text-center"><div id="qrcode" class="d-inline-block"></div></div>
        </div>
    </div>
</div>

<script>
    new QRCode(document.getElementById("qrcode"), { text: "Verify-BK-<?php echo $id; ?>", width: 120, height: 120 });
</script>
</body>
</html>