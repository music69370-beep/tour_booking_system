<?php 
include 'config/db.php'; 
/** @var array $lang */ //
// ຮັບຄ່າເບີໂທລະສັບຈາກ URL (ຄົ້ນຫາ)
$phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';
?>
<!DOCTYPE html>
<html lang="<?php echo ($current_lang == 'lao') ? 'lo' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['nav_status']; ?> - TourBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; }
        .navbar { padding: 15px 0; }
        .search-section { background: white; padding: 50px 0; border-bottom: 1px solid #eee; }
        .status-card { border: none; border-radius: 20px; transition: all 0.3s; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .status-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .lang-btn { font-size: 0.75rem; padding: 5px 12px; border-radius: 50px; text-decoration: none; border: 1px solid rgba(255,255,255,0.4); color: white; transition: 0.3s; font-weight: bold; }
        .lang-btn.active { background: white; color: #0d6efd; border-color: white; }
        .star-rating { color: #ccc; font-size: 1.5rem; cursor: pointer; transition: 0.2s; }
        .star-rating.active { color: #ffc107; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-plane-departure me-2"></i>TourBooking</a>
        <div class="ms-auto d-flex align-items-center">
            <a href="index.php" class="nav-link text-white me-3 d-none d-sm-block"><?php echo $lang['nav_home']; ?></a>
            <div class="d-flex gap-1 p-1 bg-dark bg-opacity-25 rounded-pill">
                <a href="?lang=lao&phone=<?php echo urlencode($phone); ?>" class="lang-btn <?php echo ($current_lang == 'lao') ? 'active' : ''; ?>">LAO</a>
                <a href="?lang=eng&phone=<?php echo urlencode($phone); ?>" class="lang-btn <?php echo ($current_lang == 'eng') ? 'active' : ''; ?>">ENG</a>
            </div>
        </div>
    </div>
</nav>

<!-- Search Section -->
<section class="search-section">
    <div class="container text-center">
        <h2 class="fw-bold mb-3"><?php echo $lang['nav_status']; ?></h2>
        <p class="text-muted mb-4"><?php echo ($current_lang=='lao')?'ກະລຸນາປ້ອນເບີໂທລະສັບເພື່ອຄົ້ນຫາການຈອງຂອງທ່ານ':'Please enter your phone number to find your bookings'; ?></p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="" method="GET" class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border">
                    <input type="hidden" name="lang" value="<?php echo $current_lang; ?>">
                    <input type="text" name="phone" class="form-control border-0 px-4 shadow-none" placeholder="020 xxxxxxxx" value="<?php echo htmlspecialchars($phone); ?>" required>
                    <button class="btn btn-warning px-5 fw-bold" type="submit">
                        <i class="fas fa-search me-2"></i> <?php echo ($current_lang=='lao')?'ຄົ້ນຫາ':'Search'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php 
            if ($phone != '') {
                $stmt = mysqli_prepare($conn, "SELECT b.*, c.fullname, t.tour_name, t.tour_code, t.image, t.duration
                        FROM bookings b
                        JOIN customers c ON b.customer_id = c.customer_id
                        JOIN tours t ON b.tour_id = t.tour_id
                        WHERE c.phone LIKE ?
                        ORDER BY b.booking_id DESC");
                $like_phone = '%' . $phone . '%';
                mysqli_stmt_bind_param($stmt, "s", $like_phone);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $status = $row['status'];
                        
                        // ເລືອກສີ ແລະ ຂໍ້ຄວາມຕາມສະຖານະ
                        if($status == 'Confirmed') { $bg = 'bg-success'; $label = ($current_lang=='lao')?'ຢືນຢັນແລ້ວ':'Confirmed'; }
                        elseif($status == 'Cancelled') { $bg = 'bg-danger'; $label = ($current_lang=='lao')?'ຍົກເລີກແລ້ວ':'Cancelled'; }
                        else { $bg = 'bg-warning text-dark'; $label = ($current_lang=='lao')?'ລໍຖ້າອະນຸມັດ':'Pending'; }
            ?>
                <!-- Booking Card -->
                <div class="card status-card shadow-sm mb-4 border-0">
                    <div class="card-header <?php echo $bg; ?> text-white py-3 border-0 d-flex justify-content-between align-items-center px-4">
                        <span class="fw-bold"><i class="fas fa-ticket-alt me-2"></i> #BK-<?php echo str_pad($row['booking_id'], 4, '0', STR_PAD_LEFT); ?></span>
                        <span class="badge bg-white text-dark rounded-pill px-3"><?php echo $label; ?></span>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-2 d-none d-md-block">
                                <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="img-fluid rounded-3 shadow-sm border">
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold text-dark mb-1"><?php echo $row['tour_name']; ?></h5>
                                <p class="text-muted small mb-2"><?php echo $row['duration']; ?> | ວັນທີເດີນທາງ: <strong class="text-primary"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></strong></p>
                                <div class="small">
                                    <span class="text-muted"><?php echo $lang['form_fullname']; ?>:</span> 
                                    <strong><?php echo $row['fullname']; ?></strong>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end border-start">
                                <small class="text-muted d-block"><?php echo $lang['form_total']; ?></small>
                                <h3 class="fw-bold text-danger mb-3"><?php echo number_format($row['total_price']); ?> <small class="fs-6">LAK</small></h3>
                                
                                <div class="d-grid gap-2">
                                    <?php if($status == 'Confirmed'): ?>
                                        <!-- *** ຈຸດແກ້ໄຂ: ລິ້ງໄປໜ້າ ticket.php ທີ່ຢູ່ Root *** -->
                                        <a href="ticket.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-primary btn-sm rounded-pill fw-bold">
                                            <i class="fas fa-eye me-1"></i> <?php echo ($current_lang=='lao')?'ເບິ່ງໃບຢັ້ງຢືນການຈອງ':'View Booking Ticket'; ?>
                                        </a>
                                        <button class="btn btn-warning btn-sm rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $row['booking_id']; ?>">
                                            <i class="fas fa-star me-1"></i> <?php echo ($current_lang=='lao')?'ໃຫ້ຄະແນນຄວາມປະທັບໃຈ':'Rate Your Trip'; ?>
                                        </button>
                                    <?php else: ?>
                                        <div class="alert alert-light border-0 small py-2 mb-0 text-center">
                                            <i class="fas fa-clock me-1"></i> <?php echo ($current_lang=='lao')?'ກະລຸນາລໍຖ້າການກວດສອບ':'Waiting for Verification'; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Review (ຄືເກົ່າ) -->
                <!-- Modal ສຳລັບຂຽນຣີວິວ (ແກ້ໄຂໃໝ່) -->
                <div class="modal fade" id="reviewModal<?php echo $row['booking_id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0">
                            <form action="save_review.php" method="POST">
                                <div class="modal-header border-0 bg-warning text-dark">
                                    <h5 class="modal-title fw-bold">Review Your Trip</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 text-center">
                                    <!-- ສົ່ງຂໍ້ມູນທີ່ຈຳເປັນໄປເບື້ອງຫຼັງ -->
                                    <input type="hidden" name="tour_id" value="<?php echo $row['tour_id']; ?>">
                                    <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>">
                                    <input type="hidden" name="user_phone" value="<?php echo $phone; ?>">
                                    
                                    <p class="mb-2 fw-bold">ໃຫ້ຄະແນນຄວາມປະທັບໃຈ</p>
                                    <div class="mb-4">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <input type="radio" name="rating" value="<?php echo $i; ?>" id="r-<?php echo $row['booking_id'].'-'.$i; ?>" class="d-none" required>
                                            <label for="r-<?php echo $row['booking_id'].'-'.$i; ?>" class="star-rating s-<?php echo $row['booking_id']; ?>" onclick="updateStars(<?php echo $row['booking_id']; ?>, <?php echo $i; ?>)" style="cursor:pointer;">
                                                <i class="fas fa-star"></i>
                                            </label>
                                        <?php endfor; ?>
                                    </div>
                                    <textarea name="comment" class="form-control bg-light border-0" rows="3" placeholder="ຂຽນຄຳຄິດເຫັນ..." required></textarea>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold shadow">Submit Review</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php 
                    }
                } else {
                    echo "<div class='text-center py-5'><i class='fas fa-search fa-3x text-muted opacity-25 mb-3'></i><h5 class='text-muted'>ບໍ່ພົບຂໍ້ມູນການຈອງສຳລັບເບີໂທນີ້</h5><a href='index.php' class='btn btn-link text-decoration-none'>ກັບໄປໜ້າຫຼັກ</a></div>";
                }
            }
            ?>
        </div>
    </div>
</div>

<footer class="py-4 text-center text-muted small border-top bg-white">
    <p class="mb-0">© 2026 Tour Booking System. All Rights Reserved.</p>
</footer>

<script>
function updateStars(bkId, rating) {
    // ຊອກຫາ Label ດາວທັງໝົດຂອງ Modal ນັ້ນ
    const labels = document.querySelectorAll('.s-' + bkId);
    labels.forEach((label, index) => {
        if (index < rating) {
            label.style.color = '#ffc107'; // ສີເຫຼືອງ
        } else {
            label.style.color = '#dee2e6'; // ສີເທົາ
        }
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>