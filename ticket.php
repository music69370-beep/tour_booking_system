<?php 
include 'config/db.php'; 

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

// ດຶງຂໍ້ມູນສະເພາະສິ່ງທີ່ລູກຄ້າຄວນເຫັນ
$sql = "SELECT b.*, c.fullname, c.phone, t.tour_name, t.tour_code, t.duration, t.meeting_point, 
               t.image, t.highlights, t.whats_included, t.whats_excluded
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        WHERE b.booking_id = '$id' AND b.status = 'Confirmed'"; // ໂຊສະເພາະທີ່ຢືນຢັນແລ້ວ

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) { 
    echo "<script>alert('ບໍ່ພົບຂໍ້ມູນ ຫຼື ການຈອງຍັງບໍ່ທັນໄດ້ຮັບການຢືນຢັນ'); window.location='index.php';</script>"; 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>Booking Ticket - #BK-<?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fa; }
        .ticket-card { max-width: 800px; margin: 30px auto; border: none; border-radius: 20px; }
        .ticket-header { background: #0d6efd; color: white; padding: 30px; border-radius: 20px 20px 0 0; }
        .dashed-line { border-top: 2px dashed #ddd; margin: 20px 0; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .ticket-card { margin: 0; width: 100%; border: none; }
        }
    </style>
</head>
<body>

<div class="container no-print text-center mt-4">
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow"><i class="fas fa-print me-2"></i> ພິມໃບບິນນີ້</button>
    <a href="check_status.php?phone=<?php echo $row['phone']; ?>" class="btn btn-light rounded-pill px-4 ms-2">ກັບຄືນ</a>
</div>

<div class="card ticket-card shadow-lg mb-5">
    <div class="ticket-header text-center">
        <h2 class="fw-bold mb-1">ໃບຢັ້ງຢືນການຈອງທົວ</h2>
        <p class="mb-0 opacity-75">Tour Booking Voucher</p>
    </div>
    
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center mb-4">
            <div class="col-6">
                <h5 class="fw-bold text-primary">#BK-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></h5>
                <p class="text-muted small mb-0">ວັນທີຈອງ: <?php echo date('d/m/Y', strtotime($row['booking_date'])); ?></p>
            </div>
            <div class="col-6 text-end">
                <span class="badge bg-success rounded-pill px-3 py-2">ຢືນຢັນແລ້ວ (Confirmed)</span>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="text-muted small text-uppercase fw-bold">ຂໍ້ມູນຜູ້ເດີນທາງ:</h6>
                <h5 class="fw-bold"><?php echo $row['fullname']; ?></h5>
                <p class="mb-0">ເບີໂທ: <?php echo $row['phone']; ?></p>
                <p class="mb-0">ຈຳນວນ: <?php echo $row['num_people']; ?> ຄົນ</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="text-muted small text-uppercase fw-bold">ລາຍລະອຽດການເດີນທາງ:</h6>
                <h5 class="fw-bold text-danger"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></h5>
                <p class="mb-0">ຈຸດນັດພົບ: <?php echo $row['meeting_point']; ?></p>
            </div>
        </div>

        <div class="dashed-line"></div>

        <div class="row align-items-center">
            <div class="col-md-3">
                <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="img-fluid rounded-3 shadow-sm">
            </div>
            <div class="col-md-9">
                <h5 class="fw-bold"><?php echo $row['tour_name']; ?></h5>
                <p class="text-muted small mb-0">ໄລຍະເວລາ: <?php echo $row['duration']; ?> | ລະຫັດ: <?php echo $row['tour_code']; ?></p>
            </div>
        </div>

        <div class="mt-4 p-3 bg-light rounded-3">
            <h6 class="fw-bold small text-success"><i class="fas fa-check-circle me-1"></i> ສິ່ງທີ່ລວມຢູ່ນຳ:</h6>
            <div class="small text-muted mb-0" style="white-space: pre-line;"><?php echo $row['whats_included']; ?></div>
        </div>

        <div class="mt-5 text-center">
            <h3 class="fw-bold text-dark">ຍອດເງິນທີ່ຊຳລະແລ້ວ: <?php echo number_format($row['total_price']); ?> ກີບ</h3>
            <p class="text-muted small">ກະລຸນາສະແດງໃບຢັ້ງຢືນນີ້ (ໃນມືຖື ຫຼື ພິມໃສ່ເຈ້ຍ) ໃຫ້ເຈົ້າໜ້າທີ່ໃນມື້ເດີນທາງ</p>
        </div>

        <div class="row mt-5 pt-4 text-center border-top">
            <div class="col-12">
                <p class="fw-bold text-primary mb-0">ຂອບໃຈທີ່ໃຊ້ບໍລິການກັບ TourBooking</p>
                <small class="text-muted">ຕິດຕໍ່ສອບຖາມເພີ່ມເຕີມ: 020 55889977</small>
            </div>
        </div>
    </div>
</div>

</body>
</html>