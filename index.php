<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Booking - ຈອງທົວທ່ຽວລາວ ບໍລິການລະດັບ VIP</title>
    <!-- Bootstrap 5 & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        
        :root {
            --primary-color: #0d6efd;
            --accent-color: #ff4757;
        }

        body { 
            font-family: 'Noto Sans Lao', sans-serif; 
            background-color: #ffffff; 
            scroll-behavior: smooth; 
        }

        /* Hero Section ເຕັມຈໍ */
        .hero-section {
            height: 100vh;
            min-height: 600px;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            margin-top: -82px; /* ດັນໃຫ້ຂຶ້ນໄປລອດກ້ອງ Navbar */
        }

        .hero-content h1 {
            font-size: 4rem;
            font-weight: 800;
            text-shadow: 2px 4px 10px rgba(0,0,0,0.3);
        }

        /* ປັບແຕ່ງ Card ທົວ */
        .tour-card {
            border: none;
            border-radius: 25px;
            transition: all 0.4s ease;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .tour-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .tour-img {
            height: 250px;
            object-fit: cover;
        }

        .price-tag {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--accent-color);
        }

        .section-padding {
            padding: 100px 0;
        }

        .section-title {
            position: relative;
            padding-bottom: 20px;
            margin-bottom: 50px;
            font-weight: 700;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .feature-box {
            padding: 40px;
            border-radius: 20px;
            background: #f8f9fa;
            transition: all 0.3s;
            height: 100%;
            border: 1px solid #eee;
        }
        .feature-box:hover {
            background: #e7f1ff;
            border-color: var(--primary-color);
            transform: scale(1.05);
        }

        .star-active { color: #ffc107; }
        .star-inactive { color: #ddd; }

        footer {
            background: #1a1a1a;
            color: #ccc;
            padding: 80px 0 20px;
        }

        /* ປັບແຕ່ງ Carousel ໃນ Modal */
        .carousel-item img {
            border-radius: 15px 0 0 15px;
        }
        @media (max-width: 768px) {
            .hero-content h1 { font-size: 2.5rem; }
            .carousel-item img { border-radius: 15px 15px 0 0; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">
            <i class="fas fa-plane-departure me-2"></i>TourBooking
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto fw-bold">
                <li class="nav-item"><a class="nav-link px-3 active" href="index.php">ໜ້າຫຼັກ</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#tours">ແພັກເກັດທົວ</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#status">ກວດສອບສະຖານະ</a></li>
                <li class="nav-item ms-lg-4">
                    <a href="login.php" class="btn btn-light rounded-pill px-4 text-primary shadow-sm">ສຳລັບເຈົ້າໜ້າທີ່</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section (Full-screen) -->
<header class="hero-section">
    <div class="container hero-content">
        <h1 class="display-1 fw-bold mb-3">ສະບາຍດີ! ໄປທ່ຽວໃສດີມື້ນີ້?</h1>
        <p class="fs-4 mb-5 opacity-90">ຄົ້ນພົບຄວາມມະຫັດສະຈັນຂອງເມືອງລາວ ພ້ອມບໍລິການລະດັບ VIP</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="#tours" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg">ຈອງທົວເລີຍ</a>
            <a href="#status" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3 fw-bold">ກວດສອບການຈອງ</a>
        </div>
    </div>
</header>

<!-- Why Choose Us -->
<section class="section-padding bg-white">
    <div class="container text-center">
        <h2 class="section-title">ເປັນຫຍັງຕ້ອງເລືອກພວກເຮົາ?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box shadow-sm">
                    <i class="fas fa-tags fa-3x text-primary mb-4"></i>
                    <h4 class="fw-bold">ລາຄາທີ່ດີທີ່ສຸດ</h4>
                    <p class="text-muted">ຮັບປະກັນລາຄາທີ່ຄຸ້ມຄ່າ ແລະ ໂປຣໂມຊັ່ນສຸດພິເສດໃນທຸກໆເດືອນ</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box shadow-sm">
                    <i class="fas fa-shield-alt fa-3x text-success mb-4"></i>
                    <h4 class="fw-bold">ບໍລິການປອດໄພ</h4>
                    <p class="text-muted">ທີມງານມືອາຊີບ ແລະ ມີປະກັນໄພການເດີນທາງຄຸ້ມຄອງທຸກທ່ານ</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box shadow-sm">
                    <i class="fas fa-headset fa-3x text-warning mb-4"></i>
                    <h4 class="fw-bold">ຊ່ວຍເຫຼືອ 24/7</h4>
                    <p class="text-muted">ພວກເຮົາຍິນດີໃຫ້ຄຳປຶກສາ ແລະ ແກ້ໄຂບັນຫາໃຫ້ທ່ານຕະຫຼອດ 24 ຊົ່ວໂມງ</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tour Catalog Section -->
<section id="tours" class="section-padding" style="background-color: #f0f2f5;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">ແພັກເກັດທົວທີ່ແນະນຳ</h2>
            <p class="text-muted">ສຳຜັດປະສົບການໃໝ່ໆ ກັບແພັກເກັດທີ່ພວກເຮົາຄັດສັນມາເພື່ອທ່ານ</p>
        </div>
        
        <div class="row g-4">
            <?php
            $sql = "SELECT * FROM tours WHERE status = 'Active' ORDER BY tour_id DESC";
            $result = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($result)):
                $tid = $row['tour_id'];
                
                // 1. ຄຳນວນບ່ອນນັ່ງຫວ່າງ
                $booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
                $booked_count = $booked_res['total'] ?? 0;
                $remaining = $row['max_seats'] - $booked_count;

                // 2. ຄຳນວນຄະແນນສະເລ່ຍ (Stars)
                $rating_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE tour_id = $tid AND status = 'Approved'"));
                $avg_rating = round($rating_res['avg_rating'], 1);
                $total_reviews = $rating_res['total_reviews'];
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card tour-card h-100 shadow">
                    <div class="position-relative">
                        <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="card-img-top tour-img">
                        <?php if($remaining <= 3 && $remaining > 0): ?>
                            <span class="position-absolute top-0 end-0 bg-danger text-white px-3 py-1 m-3 rounded-pill fw-bold" style="font-size: 0.75rem;">ໃກ້ຈະເຕັມ!</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-4">
                        <!-- Rating Stars -->
                        <div class="mb-2">
                            <?php if($total_reviews > 0): ?>
                                <span class="text-warning small">
                                    <?php for($i=1; $i<=5; $i++) echo ($i <= $avg_rating) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                </span>
                                <small class="text-muted ms-1">(<?php echo $avg_rating; ?>/5 ຈາກ <?php echo $total_reviews; ?> ຄົນ)</small>
                            <?php else: ?>
                                <small class="text-muted italic small">ຍັງບໍ່ມີຄະແນນ</small>
                            <?php endif; ?>
                        </div>

                        <h4 class="card-title fw-bold text-dark mb-3"><?php echo $row['tour_name']; ?></h4>
                        <div class="d-flex justify-content-between mb-4 small text-muted">
                            <span><i class="far fa-clock text-primary me-1"></i> <?php echo $row['duration']; ?></span>
                            <span><i class="fas fa-utensils text-success me-1"></i> <?php echo $row['meals']; ?> ຄາບ</span>
                            <span class="fw-bold <?php echo ($remaining <= 2) ? 'text-danger' : 'text-success'; ?>">
                                <i class="fas fa-chair me-1"></i> ຫວ່າງ <?php echo $remaining; ?>
                            </span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <small class="text-muted d-block small">ລາຄາ/ທ່ານ</small>
                                <span class="price-tag"><?php echo number_format($row['price']); ?> ກີບ</span>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modal<?php echo $tid; ?>">ຂໍ້ມູນ</button>
                                <?php if($remaining > 0): ?>
                                    <a href="booking_form.php?tour_id=<?php echo $tid; ?>" class="btn btn-primary rounded-pill px-4 shadow">ຈອງເລີຍ</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary rounded-pill px-3" disabled>ເຕັມແລ້ວ</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: Slide ຮູບ ແລະ ລາຍລະອຽດ -->
            <div class="modal fade" id="modal<?php echo $tid; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content rounded-5 border-0 overflow-hidden shadow-lg">
                        <div class="modal-body p-0">
                            <div class="row g-0">
                                <!-- ສ່ວນ Slide ຮູບພາບ (Gallery) -->
                                <div class="col-md-6 bg-dark">
                                    <?php $gal_res = mysqli_query($conn, "SELECT * FROM tour_images WHERE tour_id = $tid"); ?>
                                    <div id="carousel<?php echo $tid; ?>" class="carousel slide h-100" data-bs-ride="carousel">
                                        <div class="carousel-inner h-100">
                                            <div class="carousel-item active h-100">
                                                <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="d-block w-100 h-100" style="object-fit: cover; min-height: 450px;">
                                            </div>
                                            <?php while($gal = mysqli_fetch_assoc($gal_res)): ?>
                                            <div class="carousel-item h-100">
                                                <img src="assets/uploads/tours/<?php echo $gal['image_name']; ?>" class="d-block w-100 h-100" style="object-fit: cover; min-height: 450px;">
                                            </div>
                                            <?php endwhile; ?>
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?php echo $tid; ?>" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carousel<?php echo $tid; ?>" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                                    </div>
                                </div>
                                <!-- ສ່ວນລາຍລະອຽດ -->
                                <div class="col-md-6 p-4 p-lg-5 bg-white">
                                    <button type="button" class="btn-close float-end shadow-none" data-bs-dismiss="modal"></button>
                                    <h3 class="fw-bold text-primary mb-1"><?php echo $row['tour_name']; ?></h3>
                                    <h4 class="fw-bold text-danger mb-4"><?php echo number_format($row['price']); ?> ກີບ</h4>
                                    
                                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i>ແຜນການເດີນທາງ</h6>
                                    <div class="small text-muted mb-4" style="white-space: pre-line; max-height: 180px; overflow-y: auto;">
                                        <?php echo $row['itinerary'] ?: 'ຍັງບໍ່ມີຂໍ້ມູນແຜນການເດີນທາງ'; ?>
                                    </div>

                                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-star me-2 text-success"></i>ກິດຈະກຳຫຼັກ</h6>
                                    <p class="small text-muted mb-4"><?php echo $row['activities'] ?: 'ຍັງບໍ່ມີຂໍ້ມູນກິດຈະກຳ'; ?></p>

                                    <a href="booking_form.php?tour_id=<?php echo $tid; ?>" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow">ຈອງທົວນີ້ເລີຍ</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Tracking Section -->
<section id="status" class="section-padding bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-3 text-white">ຕິດຕາມການຈອງຂອງທ່ານ</h2>
        <p class="mb-5 fs-5 opacity-75">ປ້ອນເບີໂທລະສັບເພື່ອເບິ່ງສະຖານະການຈອງ, ໃບບິນ ແລະ ໃຫ້ຄະແນນ</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="check_status.php" method="GET" class="input-group input-group-lg shadow-lg rounded-pill overflow-hidden border-0">
                    <input type="text" name="phone" class="form-control border-0 px-4 shadow-none" placeholder="020 xxxxxxxx" required>
                    <button class="btn btn-warning px-5 fw-bold" type="submit">ຄົ້ນຫາ</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4 col-md-6">
                <h3 class="fw-bold text-white mb-4">TourBooking</h3>
                <p>ພວກເຮົາຄືບໍລິສັດທ່ອງທ່ຽວອັນດັບ 1 ທີ່ເນັ້ນຄວາມສຸກ ແລະ ຄວາມປອດໄພຂອງລູກຄ້າເປັນສຳຄັນ. ຄົ້ນພົບປະສົບການໃໝ່ກັບພວກເຮົາ.</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="#" class="btn btn-outline-light rounded-circle shadow-sm" style="width: 45px; height: 45px; line-height: 32px;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-light rounded-circle shadow-sm" style="width: 45px; height: 45px; line-height: 32px;"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="btn btn-outline-light rounded-circle shadow-sm" style="width: 45px; height: 45px; line-height: 32px;"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold text-white mb-4">ຂໍ້ມູນການຕິດຕໍ່</h5>
                <ul class="list-unstyled">
                    <li class="mb-3"><i class="fas fa-map-marker-alt text-primary me-3"></i> ນະຄອນຫຼວງວຽງຈັນ, ສປປ ລາວ</li>
                    <li class="mb-3"><i class="fas fa-phone-alt text-primary me-3"></i> 020 55889977</li>
                    <li class="mb-3"><i class="fas fa-envelope text-primary me-3"></i> info@beeptour.com</li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h5 class="fw-bold text-white mb-4">ທີ່ຕັ້ງຫ້ອງການ</h5>
                <div class="rounded-4 overflow-hidden shadow-lg" style="height: 200px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d121334.8217316314!2d102.555239!3d17.9666289!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x312468603612809d%3A0x6b245c1f0d36c1e1!2sVientiane!5e0!3m2!1sen!2sla!4v1700000000000!5m2!1sen!2sla" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
        <div class="text-center mt-5 pt-4 border-top border-secondary small">
            <p>© 2026 Tour Booking System. All Rights Reserved. Designed by Vinod.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>