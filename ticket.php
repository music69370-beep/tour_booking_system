<?php 
include 'config/db.php'; 

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

// ດຶງຂໍ້ມູນການຈອງແບບລະອຽດ
$sql = "SELECT b.*, c.fullname, c.phone, c.email, t.tour_name, t.tour_code, t.duration, t.meeting_point, 
               t.image, t.itinerary, t.whats_included
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        WHERE b.booking_id = '$id' AND b.status = 'Confirmed'";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) { 
    echo "<script>alert('ບໍ່ພົບຂໍ້ມູນ ຫຼື ການຈອງຍັງບໍ່ທັນຖືກຢັ້ງຢືນ'); window.location='index.php';</script>"; 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>Voucher #BK-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; padding-bottom: 50px; }
        
        .ticket-container { max-width: 850px; margin: 30px auto; background: white; border-radius: 30px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1); border: none; }
        
        .ticket-header { background: linear-gradient(135deg, #0d6efd 0%, #0046b8 100%); color: white; padding: 40px; position: relative; }
        .ticket-header::after { content: ''; position: absolute; bottom: -20px; left: 0; width: 100%; height: 40px; background: white; border-radius: 50% 50% 0 0; }
        
        .info-label { font-size: 0.75rem; text-transform: uppercase; color: #888; font-weight: 700; letter-spacing: 1px; margin-bottom: 2px; }
        .info-value { font-weight: 700; color: #2d3436; font-size: 1.05rem; }
        
        .seat-badge { background: #0d6efd; color: white; width: 45px; height: 45px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: 800; font-size: 1.2rem; margin-right: 8px; box-shadow: 0 5px 15px rgba(13,110,253,0.3); }
        
        .timeline-box { border-left: 2px dashed #0d6efd; margin-left: 10px; padding-left: 25px; position: relative; }
        .timeline-dot { position: absolute; left: -7px; top: 5px; width: 12px; height: 12px; background: #0d6efd; border-radius: 50%; }
        
        .qr-wrapper { background: #f8f9fc; padding: 15px; border-radius: 20px; display: inline-block; border: 1px solid #eee; }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .ticket-container { margin: 0; box-shadow: none; width: 100%; border-radius: 0; }
            .ticket-header { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="container no-print text-center mt-4">
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-5 fw-bold shadow"><i class="fas fa-print me-2"></i> ພິມໃບບິນ / ບັນທຶກເປັນ PDF</button>
    <a href="check_status.php?phone=<?php echo $row['phone']; ?>" class="btn btn-light rounded-pill px-4 border ms-2">ຍ້ອນກັບ</a>
</div>

<div class="ticket-container">
    <!-- Header -->
    <div class="ticket-header text-center">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-plane-departure me-2"></i>TourBooking</h4>
            <div class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold">E-VOUCHER / CONFIRMED</div>
        </div>
        <h1 class="display-5 fw-bold mb-1">ໃບຢັ້ງຢືນການຈອງທົວ</h1>
        <p class="opacity-75 mb-0">Booking ID: #BK-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></p>
    </div>

    <div class="p-4 p-md-5 pt-5">
        <div class="row g-5">
            <!-- ຂໍ້ມູນລູກຄ້າ ແລະ ທົວ -->
            <div class="col-md-8">
                <div class="row g-4">
                    <div class="col-6">
                        <div class="info-label">ຊື່ຜູ້ເດີນທາງ (Traveler)</div>
                        <div class="info-value"><?php echo $row['fullname']; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">ເບີໂທລະສັບ (WhatsApp)</div>
                        <div class="info-value"><?php echo $row['phone']; ?></div>
                    </div>
                    <div class="col-12">
                        <div class="info-label">ແພັກເກັດທົວ (Tour Package)</div>
                        <div class="info-value text-primary fs-4"><?php echo $row['tour_name']; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">ວັນທີເດີນທາງ (Date)</div>
                        <div class="info-value text-danger"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">ໄລຍະເວລາ (Duration)</div>
                        <div class="info-value"><?php echo $row['duration']; ?></div>
                    </div>
                    <div class="col-12">
                        <div class="info-label">ຈຸດນັດພົບ (Meeting Point)</div>
                        <div class="info-value small"><?php echo $row['meeting_point']; ?></div>
                    </div>
                </div>

                <div class="mt-5">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-route me-2 text-primary"></i>ແຜນການເດີນທາງ (Itinerary)</h6>
                    <?php 
                    $itinerary = json_decode($row['itinerary'], true);
                    if($itinerary):
                        foreach($itinerary as $day):
                    ?>
                        <div class="mb-4">
                            <div class="fw-bold text-primary mb-2">Day <?php echo $day['day']; ?></div>
                            <?php foreach($day['events'] as $ev): ?>
                                <div class="timeline-box">
                                    <div class="timeline-dot"></div>
                                    <div class="fw-bold small text-dark"><?php echo htmlspecialchars($ev['location']); ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($ev['desc']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- ເບື້ອງຂວາ: QR ແລະ ບ່ອນນັ່ງ -->
            <div class="col-md-4 text-center border-start ps-md-5">
                <div class="qr-wrapper mb-4">
                    <div id="qrcode"></div>
                    <div class="mt-2 small fw-bold text-muted">SCAN TO VERIFY</div>
                </div>

                <div class="mb-4">
                    <div class="info-label">ບ່ອນນັ່ງຂອງທ່ານ (Your Seats)</div>
                    <div class="mt-2">
                        <?php 
                        $seats = explode(',', $row['selected_seats']);
                        foreach($seats as $s) echo "<span class='seat-badge'>$s</span>";
                        ?>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-4 mb-4">
                    <div class="info-label">ຍອດເງິນທີ່ຊຳລະແລ້ວ</div>
                    <div class="display-6 fw-bold text-success mt-1"><?php echo number_format($row['total_price']); ?></div>
                    <small class="fw-bold text-muted">LAK</small>
                </div>

                <div class="text-start small text-muted">
                    <p class="fw-bold text-dark mb-1">ສິ່ງທີ່ລວມຢູ່ນຳ:</p>
                    <div style="font-size: 0.7rem; white-space: pre-line;"><?php echo htmlspecialchars($row['whats_included']); ?></div>
                </div>
            </div>
        </div>
        
        <div class="mt-5 pt-4 border-top text-center">
            <p class="text-muted small">ກະລຸນາສະແດງໃບຢັ້ງຢືນນີ້ (ໃນມືຖື ຫຼື ພິມໃສ່ເຈ້ຍ) ໃຫ້ເຈົ້າໜ້າທີ່ໃນມື້ເດີນທາງ</p>
            <div class="d-flex justify-content-center gap-4 mt-3">
                <span class="small"><i class="fas fa-phone-alt me-1"></i> 020 55889977</span>
                <span class="small"><i class="fas fa-globe me-1"></i> www.tourbooking.com</span>
            </div>
        </div>
    </div>
</div>

<script>
    // ສ້າງ QR Code ຈາກ Booking ID
    new QRCode(document.getElementById("qrcode"), {
        text: "Verify-BK-<?php echo $id; ?>",
        width: 140,
        height: 140,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>

</body>
</html>