<?php 
include 'config/db.php'; 

$phone = isset($_GET['phone']) ? mysqli_real_escape_string($conn, $_GET['phone']) : '';
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ກວດສອບສະຖານະການຈອງ - TourBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fa; }
        .status-card { border: none; border-radius: 20px; transition: all 0.3s; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-plane-departure me-2"></i>TourBooking</a>
        <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">ກັບໄປໜ້າຫຼັກ</a>
    </div>
</nav>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">ສະຖານະການຈອງຂອງທ່ານ</h2>
        <p class="text-muted">ຄົ້ນຫາດ້ວຍເບີໂທລະສັບ: <span class="text-primary fw-bold"><?php echo $phone; ?></span></p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php 
            if ($phone != '') {
                $sql = "SELECT b.*, c.fullname, t.tour_name, t.duration, v.model as car_model, v.plate_number
                        FROM bookings b
                        JOIN customers c ON b.customer_id = c.customer_id
                        JOIN tours t ON b.tour_id = t.tour_id
                        LEFT JOIN vehicles v ON t.vehicle_id = v.vehicle_id
                        WHERE c.phone LIKE '%$phone%'
                        ORDER BY b.booking_id DESC";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $status = $row['status'];
                        $bg_color = ($status == 'Confirmed') ? 'bg-success' : (($status == 'Cancelled') ? 'bg-danger' : 'bg-warning text-dark');
            ?>
                <div class="card status-card shadow-sm mb-4 overflow-hidden">
                    <div class="card-header <?php echo $bg_color; ?> text-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-ticket-alt me-2"></i> ເລກທີການຈອງ: #BK-<?php echo $row['booking_id']; ?></span>
                        <span class="badge bg-white text-dark rounded-pill"><?php echo $status; ?></span>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <small class="text-muted d-block small mb-1">ຊື່ລູກຄ້າ:</small>
                                <h6 class="fw-bold"><?php echo $row['fullname']; ?></h6>
                                <small class="text-muted d-block small mt-3 mb-1">ວັນທີຈອງ:</small>
                                <h6 class="fw-bold"><?php echo date('d/m/Y H:i', strtotime($row['booking_date'])); ?></h6>
                            </div>
                            <div class="col-md-4 border-start">
                                <small class="text-muted d-block small mb-1">ແພັກເກັດທົວ:</small>
                                <h6 class="text-primary fw-bold"><?php echo $row['tour_name']; ?></h6>
                                <p class="small mb-0 text-muted"><?php echo $row['duration']; ?></p>
                                
                                <small class="text-muted d-block small mt-3 mb-1">ພາຫະນະທີ່ໃຊ້:</small>
                                <h6 class="fw-bold small text-dark">
                                    <?php echo $row['car_model'] ?: 'ຍັງບໍ່ໄດ້ກຳນົດ'; ?> 
                                    (<?php echo $row['plate_number'] ?: '-- --'; ?>)
                                </h6>
                            </div>
                            <div class="col-md-4 text-md-end border-start">
                                <small class="text-muted d-block small mb-1">ຍອດເງິນລວມ:</small>
                                <h3 class="fw-bold text-danger"><?php echo number_format($row['total_price']); ?> ກີບ</h3>
                                <small class="text-muted small"><?php echo $row['num_people']; ?> ຄົນ</small>
                                
                                <div class="mt-4">
                                    <?php if($status == 'Confirmed'): ?>
                                        <a href="pages/bookings/view.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                            <i class="fas fa-print me-1"></i> ພິມໃບບິນ
                                        </a>
                                    <?php else: ?>
                                        <p class="text-warning small mb-0"><i class="fas fa-info-circle me-1"></i> ລໍຖ້າແອດມິນຢືນຢັນການຊຳລະ</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                    }
                } else {
                    echo "<div class='alert alert-light text-center border-0 shadow-sm p-5 rounded-4'>
                            <i class='fas fa-search fa-3x mb-3 text-muted opacity-25'></i>
                            <h4 class='text-muted'>ບໍ່ພົບຂໍ້ມູນການຈອງ</h4>
                            <p class='text-muted small'>ກະລຸນາກວດສອບເບີໂທລະສັບຂອງທ່ານຄືນໃໝ່</p>
                            <a href='index.php' class='btn btn-primary rounded-pill px-4 mt-2'>ກັບໄປໜ້າຫຼັກ</a>
                          </div>";
                }
            }
            ?>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted small mt-5">
    <p>© 2026 Tour Booking System. All Rights Reserved.</p>
</footer>

</body>
</html>