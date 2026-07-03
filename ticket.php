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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap');
        :root { --primary: #0061ff; --dark: #2d3436; --light-bg: #f8faff; --success: #198754; }
        
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #dfe6e9; margin: 0; padding: 20px; color: var(--dark); }

        .ticket-container { 
            max-width: 1100px; margin: 0 auto; background: white; border-radius: 25px; 
            overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.1); display: flex; flex-direction: column;
        }

        .ticket-header { background: linear-gradient(135deg, #0061ff 0%, #60a5fa 100%); color: white; padding: 20px; text-align: center; }
        .ticket-header h2 { margin: 0; font-size: 1.6rem; font-weight: 700; }

        .ticket-content { display: flex; padding: 25px; gap: 25px; background: white; }
        .col-left { flex: 1.4; border-right: 1px dashed #eee; padding-right: 20px; }
        .col-right { flex: 0.6; display: flex; flex-direction: column; }

        .tour-name { color: var(--primary); font-size: 1.3rem; font-weight: 700; margin-bottom: 12px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; }
        .info-item { background: var(--light-bg); padding: 8px 12px; border-radius: 10px; }
        .info-label { font-size: 0.65rem; color: #888; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; display: block; }
        .info-value { font-size: 0.85rem; font-weight: 700; }

        #map { height: 180px; border-radius: 12px; border: 1px solid #eee; margin-bottom: 15px; z-index: 1; }

        .section-box { background: #fff; border: 1px solid #f1f3f7; padding: 12px; border-radius: 12px; margin-bottom: 12px; }
        .section-title { font-weight: 700; font-size: 0.75rem; color: var(--primary); margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 4px; display: flex; align-items: center; gap: 5px; }

        /* Itinerary Timeline Style */
        .iti-container { max-height: 200px; overflow: hidden; }
        .day-label { font-size: 0.75rem; font-weight: 800; color: var(--primary); margin-bottom: 5px; }
        .timeline-item { position: relative; padding-left: 15px; border-left: 1.5px solid #0061ff; margin-left: 5px; padding-bottom: 5px; }
        .timeline-item::before { content: ''; position: absolute; left: -5px; top: 4px; width: 8px; height: 8px; background: white; border: 1.5px solid #0061ff; border-radius: 50%; }
        .iti-loc { font-size: 0.8rem; font-weight: 700; color: #333; }
        .iti-desc { font-size: 0.7rem; color: #777; }

        /* Rooming List Style */
        .hotel-name { font-size: 0.75rem; font-weight: 700; color: #555; background: #eee; padding: 2px 8px; border-radius: 4px; margin-bottom: 4px; }
        .room-row { display: flex; justify-content: space-between; font-size: 0.75rem; padding: 2px 5px; }

        .total-price { color: var(--success); font-size: 1.8rem; font-weight: 800; text-align: center; margin: 5px 0; }
        
        .qr-wrapper { text-align: center; margin-top: 10px; }
        .qr-box { 
            display: inline-block; padding: 8px; background: white; border-radius: 12px; 
            border: 1px solid #eee; width: 100px; height: 100px; box-sizing: content-box; 
        }

        @media print {
            @page { size: A4 landscape; margin: 0.4cm; }
            body { background: white; padding: 0; }
            .no-print { display: none; }
            .ticket-container { box-shadow: none; border: 1px solid #eee; width: 100%; border-radius: 15px; }
            .ticket-content { padding: 15px; gap: 15px; }
            .col-left { padding-right: 15px; }
            .total-price { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="container no-print text-center mb-3">
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-5 fw-bold shadow"><i class="fas fa-print me-2"></i> ພິມໃບບັດການຈອງ (Voucher)</button>
</div>

<div class="ticket-container">
    <div class="ticket-header">
        <h2>ໃບຢັ້ງຢືນການຈອງທົວ</h2>
        <small>VOUCHER NO: #BK-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></small>
    </div>

    <div class="ticket-content">
        <!-- ຟາກຊ້າຍ: ຂໍ້ມູນ & ແຜນທີ່ & ລາຍລະອຽດການໄປ -->
        <div class="col-left">
            <div class="tour-name"><?php echo e($row['tour_name']); ?></div>
            
            <div class="info-grid">
                <div class="info-item"><span class="info-label">ຊື່ຜູ້ຈອງ (Lead)</span><div class="info-value"><?php echo e($row['fullname']); ?></div></div>
                <div class="info-item"><span class="info-label">ວັນທີເດີນທາງ</span><div class="info-value"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></div></div>
                <div class="info-item"><span class="info-label">ຈຳນວນ</span><div class="info-value"><?php echo $row['num_people']; ?> ທ່ານ</div></div>
                <div class="info-item"><span class="info-label">ຈຸດນັດພົບ</span><div class="info-value text-truncate"><?php echo e($row['meeting_point']); ?></div></div>
            </div>

            <div class="section-title"><i class="fas fa-map-marked-alt"></i> ແຜນທີ່ເສັ້ນທາງເດີນທາງ</div>
            <div id="map"></div>

            <div class="section-title"><i class="fas fa-route"></i> ລາຍລະອຽດແຜນການເດີນທາງ (Itinerary)</div>
            <div class="iti-container">
                <div class="row g-3">
                    <?php 
                    $itinerary = json_decode($row['itinerary'], true); 
                    if($itinerary):
                        foreach($itinerary as $day): ?>
                            <div class="col-6">
                                <div class="day-label">ມື້ທີ <?php echo $day['day']; ?></div>
                                <?php foreach($day['events'] as $ev): ?>
                                    <div class="timeline-item">
                                        <div class="iti-loc"><?php echo $ev['location']; ?></div>
                                        <div class="iti-desc"><?php echo $ev['desc']; ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach;
                    endif; ?>
                </div>
            </div>
        </div>

        <!-- ຟາກຂວາ: Rooming, Included, QR -->
        <div class="col-right">
            <div class="section-box">
                <div class="section-title"><i class="fas fa-bed"></i> ການຈັດສັນຫ້ອງພັກ (Rooming)</div>
                <?php 
                $ra_res = mysqli_query($conn, "SELECT * FROM booking_room_assignments WHERE booking_id = '$id' ORDER BY hotel_name ASC");
                $curr_h = "";
                while($ra = mysqli_fetch_assoc($ra_res)): 
                    if($curr_h != $ra['hotel_name']): $curr_h = $ra['hotel_name']; ?>
                        <div class="hotel-name"><i class="fas fa-hotel"></i> <?php echo $curr_h; ?></div>
                    <?php endif; ?>
                    <div class="room-row">
                        <span><?php echo $ra['participant_name']; ?></span>
                        <span class="fw-bold text-danger">ຫ້ອງ: <?php echo $ra['room_number'] ?: '...'; ?></span>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="section-box" style="flex-grow: 1;">
                <div class="section-title"><i class="fas fa-check-circle"></i> ສິ່ງທີ່ລວມຢູ່ນຳ</div>
                <div style="font-size: 0.7rem; color: #555; line-height: 1.4;">
                    <?php echo nl2br(e($row['whats_included'])); ?>
                </div>
            </div>

            <div class="qr-wrapper">
                <span class="info-label">ຍອດເງິນຊຳລະແລ້ວ</span>
                <div class="total-price"><?php echo number_format($row['total_price']); ?> <small style="font-size: 0.8rem">LAK</small></div>
                <div class="qr-box shadow-sm">
                    <div id="qrcode"></div>
                </div>
                <p style="font-size: 0.65rem; color: #999; margin-top: 5px;">ກະລຸນາສະແດງ Voucher ນີ້ໃນມື້ເດີນທາງ</p>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. QR Code
    new QRCode(document.getElementById("qrcode"), { text: "Verify-BK-<?php echo $id; ?>", width: 100, height: 100 });

    // 2. Map
    const itiData = <?php echo $row['itinerary'] ?: '[]'; ?>;
    const map = L.map('map', { zoomControl: false, attributionControl: false }).setView([17.9757, 102.6331], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    let pts = [];
    itiData.forEach(d => {
        d.events.forEach(e => {
            if(e.lat && e.lng) {
                const p = [parseFloat(e.lat), parseFloat(e.lng)];
                pts.push(p);
                L.marker(p).addTo(map);
            }
        });
    });
    if(pts.length > 1) {
        L.polyline(pts, {color: '#0061ff', weight: 3, dashArray: '5, 10'}).addTo(map);
        map.fitBounds(L.polyline(pts).getBounds(), {padding: [20,20]});
    } else if(pts.length === 1) { map.setView(pts[0], 14); }
</script>
</body>
</html>