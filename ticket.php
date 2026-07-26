<?php 
include 'config/db.php'; 
/** @var mysqli $conn */
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

// ປັບ SQL ໃໝ່ເພື່ອ JOIN ເອົາຂໍ້ມູນ ຜູ້ອະນຸມັດ, ລົດ ແລະ ຄົນຂັບ
$sql = "SELECT b.*, c.fullname, c.phone, c.email, 
               t.tour_name, t.tour_code, t.duration, t.meeting_point, t.image, t.itinerary, t.whats_included,
               u.fullname as admin_name,
               v.model as vehicle_model, v.plate_number,
               d.fullname as driver_name, d.phone as driver_phone
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        LEFT JOIN users u ON b.approved_by = u.user_id
        -- ເຊື່ອມຫາລົດ ແລະ ຄົນຂັບ ທີ່ຖືກປ່ອຍອອກທົວໃນວັນທີນັ້ນ
        LEFT JOIN vehicle_outings vo ON (b.tour_id = vo.tour_id AND b.travel_date = vo.start_date)
        LEFT JOIN vehicles v ON vo.vehicle_id = v.vehicle_id
        LEFT JOIN drivers d ON vo.driver_id = d.driver_id
        WHERE b.booking_id = '$id' AND b.status = 'Confirmed'";

$query = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($query);

if (!$row) { echo "<script>alert('ບໍ່ພົບຂໍ້ມູນ ຫຼື ການຈອງຍັງບໍ່ທັນໄດ້ຮັບການອະນຸມັດ'); window.location='index.php';</script>"; exit; }
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>Voucher #BK-<?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap');
        :root { --primary: #0061ff; --dark: #2d3436; --light-bg: #f8faff; --success: #198754; }
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #dfe6e9; margin: 0; padding: 20px; color: var(--dark); }
        .ticket-container { max-width: 1100px; margin: 0 auto; background: white; border-radius: 25px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        .ticket-header { background: linear-gradient(135deg, #0061ff 0%, #60a5fa 100%); color: white; padding: 20px; text-align: center; }
        .ticket-content { display: flex; padding: 25px; gap: 25px; background: white; }
        .col-left { flex: 1.4; border-right: 1px dashed #eee; padding-right: 20px; }
        .col-right { flex: 0.6; display: flex; flex-direction: column; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; }
        .info-item { background: var(--light-bg); padding: 8px 12px; border-radius: 10px; }
        .info-label { font-size: 0.65rem; color: #888; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; display: block; }
        .info-value { font-size: 0.85rem; font-weight: 700; }
        #map { height: 180px; border-radius: 12px; border: 1px solid #eee; margin-bottom: 15px; z-index: 1; }
        .section-box { background: #fff; border: 1px solid #f1f3f7; padding: 12px; border-radius: 12px; margin-bottom: 12px; }
        .section-title { font-weight: 700; font-size: 0.75rem; color: var(--primary); margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 4px; display: flex; align-items: center; gap: 5px; }
        .timeline-item { position: relative; padding-left: 15px; border-left: 1.5px solid #0061ff; margin-left: 5px; padding-bottom: 5px; }
        .transport-badge { background: #fff4e5; border: 1px solid #ff9800; border-radius: 10px; padding: 10px; margin-top: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="container no-print text-center mb-3">
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-5 fw-bold shadow"><i class="fas fa-print me-2"></i> ພິມໃບ Voucher</button>
</div>

<div class="ticket-container">
    <div class="ticket-header">
        <h2 class="mb-0">ໃບຢັ້ງຢືນການຈອງທົວ (Tour Voucher)</h2>
        <small>VOUCHER NO: #BK-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></small>
    </div>

    <div class="ticket-content">
        <div class="col-left">
            <h4 class="fw-bold text-primary mb-3"><?php echo $row['tour_name']; ?></h4>
            
            <div class="info-grid">
                <div class="info-item"><span class="info-label">ຊື່ຜູ້ຈອງ (Lead)</span><div class="info-value"><?php echo $row['fullname']; ?></div></div>
                <div class="info-item"><span class="info-label">ວັນທີເດີນທາງ</span><div class="info-value"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></div></div>
                <div class="info-item"><span class="info-label">ຈຸດນັດພົບ</span><div class="info-value text-truncate"><?php echo $row['meeting_point']; ?></div></div>
                <div class="info-item"><span class="info-label">ຜູ້ອະນຸມັດ (Approved by)</span><div class="info-value text-success"><?php echo $row['admin_name'] ?: 'System'; ?></div></div>
            </div>

            <!-- ສ່ວນຂໍ້ມູນລົດ ແລະ ຄົນຂັບ -->
            <div class="section-title"><i class="fas fa-bus"></i> ຂໍ້ມູນການເດີນທາງ (Transportation)</div>
            <div class="transport-badge">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted d-block">ລົດທົວ:</small>
                        <b class="small"><?php echo $row['vehicle_model'] ?: 'ລໍຖ້າການຈັດລົດ'; ?></b><br>
                        <span class="badge bg-dark small"><?php echo $row['plate_number']; ?></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">ຄົນຂັບ:</small>
                        <b class="small"><?php echo $row['driver_name'] ?: '---'; ?></b><br>
                        <span class="text-primary small"><i class="fas fa-phone-alt"></i> <?php echo $row['driver_phone'] ?: '---'; ?></span>
                    </div>
                </div>
            </div>

            <div class="section-title mt-4"><i class="fas fa-map-marked-alt"></i> ແຜນທີ່ເສັ້ນທາງ</div>
            <div id="map"></div>

            <div class="section-title"><i class="fas fa-route"></i> ແຜນການເດີນທາງ (Itinerary)</div>
            <div style="max-height: 150px; overflow-y: auto; padding-right: 10px;">
                <?php $iti = json_decode($row['itinerary'], true); if($iti) foreach($iti as $day): ?>
                    <div class="small fw-bold text-primary">ມື້ທີ <?php echo $day['day']; ?></div>
                    <?php foreach($day['events'] as $ev): ?>
                        <div class="timeline-item"><div class="small fw-bold"><?php echo $ev['location']; ?></div></div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-right">
            <div class="section-box">
                <div class="section-title"><i class="fas fa-bed"></i> ການຈັດສັນຫ້ອງພັກ</div>
                <?php 
                $ra_res = mysqli_query($conn, "SELECT * FROM booking_room_assignments WHERE booking_id = '$id'");
                while($ra = mysqli_fetch_assoc($ra_res)): ?>
                    <div class="d-flex justify-content-between small border-bottom py-1">
                        <span><?php echo $ra['participant_name']; ?></span>
                        <b class="text-danger">ຫ້ອງ: <?php echo $ra['room_number']; ?></b>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="section-box" style="flex-grow: 1;">
                <div class="section-title"><i class="fas fa-check-circle"></i> ສິ່ງທີ່ລວມຢູ່ນຳ</div>
                <div class="small text-muted"><?php echo nl2br($row['whats_included']); ?></div>
            </div>

            <div class="text-center mt-3">
                <span class="info-label">ຍອດເງິນຊຳລະແລ້ວ</span>
                <h3 class="fw-bold text-success">₭ <?php echo number_format($row['total_price']); ?></h3>
                <div id="qrcode" class="d-inline-block p-2 border rounded-3 bg-white"></div>
                <p class="small text-muted mt-2" style="font-size: 10px;">ກະລຸນາສະແດງ Voucher ນີ້ໃນມື້ເດີນທາງ</p>
            </div>
        </div>
    </div>
</div>

<script>
    new QRCode(document.getElementById("qrcode"), { text: "Verified-BK-<?php echo $id; ?>", width: 90, height: 90 });
    const itiData = <?php echo $row['itinerary'] ?: '[]'; ?>;
    const map = L.map('map', { zoomControl: false, attributionControl: false }).setView([17.9757, 102.6331], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    let pts = [];
    itiData.forEach(d => d.events.forEach(e => { if(e.lat && e.lng) { let p=[e.lat, e.lng]; pts.push(p); L.marker(p).addTo(map); } }));
    if(pts.length > 0) map.fitBounds(L.polyline(pts).getBounds(), {padding:[20,20]});
</script>
</body>
</html>