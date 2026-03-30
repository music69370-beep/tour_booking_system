<?php include 'config/db.php'; 

// ຮັບຄ່າການຄົ້ນຫາ ແລະ ກັ່ນຕອງ
$search_keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$category_filter = isset($_GET['cat']) ? mysqli_real_escape_string($conn, $_GET['cat']) : 'all';
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Booking - <?php echo $lang['hero_title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        :root { --primary-color: #0d6efd; --accent-color: #ff4757; }
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fa; scroll-behavior: smooth; }

        /* Navbar & Hero */
        .navbar { padding: 15px 0; }
        .hero-section {
            height: 60vh; min-height: 400px;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=1920&q=80');
            background-size: cover; background-position: center;
            display: flex; align-items: center; justify-content: center;
            color: white; text-align: center; margin-top: -82px;
        }

        /* Search Box */
        .search-container { margin-top: -60px; z-index: 20; position: relative; }
        .search-box { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 15px 40px rgba(0,0,0,0.1); border: 1px solid #eee; }

        .filter-btn {
            border: 1px solid #ddd; background: white; padding: 8px 22px; border-radius: 50px;
            color: #555; text-decoration: none; font-size: 0.9rem; transition: all 0.3s;
            display: inline-block; margin: 5px;
        }
        .filter-btn:hover, .filter-btn.active { background: var(--primary-color); color: white; border-color: var(--primary-color); }

        /* --- ປັບແຕ່ງ Card ທົວໃໝ່ໃຫ້ລະອຽດຂຶ້ນ --- */
        .tour-card {
            border: none; border-radius: 25px; transition: all 0.4s ease;
            overflow: hidden; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            display: flex; flex-direction: column; height: 100%;
        }
        .tour-card:hover { transform: translateY(-15px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .tour-img-container { position: relative; height: 220px; overflow: hidden; }
        .tour-img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .tour-card:hover .tour-img { scale: 1.1; }
        
        .category-badge { position: absolute; top: 15px; left: 15px; background: var(--primary-color); color: white; padding: 5px 15px; border-radius: 50px; font-size: 0.75rem; font-weight: bold; z-index: 10; }
        .price-tag { font-size: 1.4rem; font-weight: 700; color: var(--accent-color); }
        
        .info-item { font-size: 0.85rem; color: #666; margin-bottom: 8px; }
        .info-item i { width: 20px; color: var(--primary-color); }

        section { padding: 80px 0; }
        .old-footer { background: #1a1a1a; color: white; padding: 60px 0 30px; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php"><i class="fas fa-plane-departure me-2"></i>TourBooking</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto fw-bold align-items-center">
                <li class="nav-item"><a class="nav-link px-3 active" href="index.php"><?php echo $lang['nav_home']; ?></a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#tours"><?php echo $lang['nav_tours']; ?></a></li>
                <li class="nav-item"><a class="nav-link px-3" href="check_status.php"><?php echo $lang['nav_status']; ?></a></li>
                <li class="nav-item d-flex gap-1 ms-lg-3 p-1 bg-dark bg-opacity-25 rounded-pill">
                    <a href="?lang=lao" class="lang-btn <?php echo ($current_lang == 'lao') ? 'active' : ''; ?>">LAO</a>
                    <a href="?lang=eng" class="lang-btn <?php echo ($current_lang == 'eng') ? 'active' : ''; ?>">ENG</a>
                </li>
                <li class="nav-item ms-lg-3"><a href="login.php" class="btn btn-light rounded-pill px-4 text-primary shadow-sm small"><?php echo $lang['nav_admin']; ?></a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-section">
    <div class="container">
        <h1 class="display-3 fw-bold mb-3"><?php echo $lang['hero_title']; ?></h1>
        <p class="fs-5 opacity-90"><?php echo $lang['hero_subtitle']; ?></p>
    </div>
</header>

<!-- Search & Filter -->
<div class="container search-container">
    <div class="search-box border">
        <form action="index.php#tours" method="GET">
            <div class="row g-3">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill px-3"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control border-start-0 rounded-end-pill py-3 shadow-none" placeholder="ຄົ້ນຫາ..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow"><?php echo ($current_lang=='lao')?'ຄົ້ນຫາທົວ':'Search'; ?></button>
                </div>
            </div>
        </form>
        <div class="text-center mt-4 d-flex flex-wrap justify-content-center">
            <a href="index.php?cat=all#tours" class="filter-btn <?php echo ($category_filter == 'all') ? 'active' : ''; ?>">ທັງໝົດ</a>
            <a href="index.php?cat=ທົວວັດທະນະທຳ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວວັດທະນະທຳ') ? 'active' : ''; ?>">ວັດທະນະທຳ</a>
            <a href="index.php?cat=ທົວຜະຈົນໄພ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວຜະຈົນໄພ') ? 'active' : ''; ?>">ຜະຈົນໄພ</a>
            <a href="index.php?cat=ທົວພັກຜ່ອນ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວພັກຜ່ອນ') ? 'active' : ''; ?>">ພັກຜ່ອນ</a>
        </div>
    </div>
</div>

<!-- Tour Catalog Section -->
<section id="tours">
    <div class="container">
        <h2 class="fw-bold text-center mb-5"><?php echo $lang['tour_recommend']; ?></h2>
        
        <div class="row g-4">
            <?php
            $sql = "SELECT * FROM tours WHERE status = 'Active'";
            if ($category_filter != 'all') { $sql .= " AND category = '$category_filter'"; }
            if ($search_keyword != '') { $sql .= " AND (tour_name LIKE '%$search_keyword%' OR highlights LIKE '%$search_keyword%')"; }
            $sql .= " ORDER BY tour_id DESC";
            $result = mysqli_query($conn, $sql);

            while($row = mysqli_fetch_assoc($result)):
                $tid = $row['tour_id'];
                $booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
                $remaining = $row['max_seats'] - ($booked_res['total'] ?? 0);
                $rating_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg_r, COUNT(*) as count FROM reviews WHERE tour_id = $tid AND status = 'Approved'"));
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card tour-card bg-white">
                    <div class="tour-img-container">
                        <span class="category-badge shadow-sm"><?php echo $row['category']; ?></span>
                        <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="tour-img">
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Rating -->
                        <div class="mb-2">
                            <?php if($rating_res['count'] > 0): ?>
                                <span class="text-warning small"><i class="fas fa-star"></i> <?php echo round($rating_res['avg_r'], 1); ?>/5</span>
                                <small class="text-muted">(<?php echo $rating_res['count']; ?>)</small>
                            <?php else: ?>
                                <small class="text-muted small italic">ຍັງບໍ່ມີຄະແນນ</small>
                            <?php endif; ?>
                        </div>

                        <h4 class="card-title fw-bold text-dark mb-3"><?php echo $row['tour_name']; ?></h4>
                        
                        <!-- ລາຍລະອຽດເພີ່ມເຕີມທີ່ລູກຄ້າຢາກເຫັນ -->
                        <div class="info-item"><i class="fas fa-calendar-alt"></i> ວັນທີ: <strong><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></strong></div>
                        <div class="info-item"><i class="far fa-clock"></i> ໄລຍະເວລາ: <?php echo $row['duration']; ?></div>
                        <div class="info-item"><i class="fas fa-utensils"></i> ອາຫານລວມ: <?php echo $row['meals']; ?> ຄາບ</div>
                        <div class="info-item">
                            <i class="fas fa-chair"></i> ບ່ອນນັ່ງຫວ່າງ: 
                            <span class="<?php echo ($remaining <= 2)?'text-danger fw-bold':'text-success'; ?>"><?php echo $remaining; ?> / <?php echo $row['max_seats']; ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                            <div>
                                <small class="text-muted d-block small">ລາຄາ/ທ່ານ</small>
                                <span class="price-tag"><?php echo number_format($row['price']); ?> ກີບ</span>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modal<?php echo $tid; ?>">ຂໍ້ມູນ</button>
                                <?php if($remaining > 0): ?>
                                    <a href="booking_form.php?tour_id=<?php echo $tid; ?>" class="btn btn-primary rounded-pill px-4 shadow">ຈອງ</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary rounded-pill px-3" disabled>ເຕັມ</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: ລາຍລະອຽດແບບຈັດເຕັມ -->
            <div class="modal fade" id="modal<?php echo $tid; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content rounded-5 border-0 overflow-hidden">
                        <div class="modal-body p-0">
                            <div class="row g-0">
                                <div class="col-lg-7 bg-dark d-flex align-items-center">
                                    <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="w-100" style="object-fit: contain; max-height: 500px;">
                                </div>
                                <div class="col-lg-5 p-4 p-lg-5 bg-white overflow-auto" style="max-height: 90vh;">
                                    <button type="button" class="btn-close float-end shadow-none" data-bs-dismiss="modal"></button>
                                    <span class="badge bg-primary mb-2"><?php echo $row['category']; ?></span>
                                    <h2 class="fw-bold mb-1 text-dark"><?php echo $row['tour_name']; ?></h2>
                                    <p class="text-muted small mb-4">ລະຫັດ: <?php echo $row['tour_code']; ?> | <i class="fas fa-map-marker-alt text-danger"></i> ນັດພົບ: <?php echo $row['meeting_point']; ?></p>
                                    
                                    <h6 class="fw-bold text-primary mb-2">ຈຸດເດັ່ນຂອງການເດີນທາງ:</h6>
                                    <p class="small text-muted mb-4" style="white-space: pre-line;"><?php echo $row['highlights']; ?></p>

                                    <div class="row mb-4">
                                        <div class="col-6">
                                            <h6 class="fw-bold text-success small">ລວມຢູ່ນຳ:</h6>
                                            <div class="small text-muted" style="white-space: pre-line; font-size: 0.75rem;"><?php echo $row['whats_included']; ?></div>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="fw-bold text-danger small">ບໍ່ລວມຢູ່ນຳ:</h6>
                                            <div class="small text-muted" style="white-space: pre-line; font-size: 0.75rem;"><?php echo $row['whats_excluded']; ?></div>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-2">ແຜນການເດີນທາງ:</h6>
                                    <div class="bg-light p-3 rounded-4 small text-muted mb-4" style="white-space: pre-line;"><?php echo $row['itinerary']; ?></div>

                                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                        <h3 class="fw-bold text-danger mb-0"><?php echo number_format($row['price']); ?> ກີບ</h3>
                                        <a href="booking_form.php?tour_id=<?php echo $tid; ?>" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">ຈອງຕອນນີ້</a>
                                    </div>
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

<!-- Footer -->
<footer class="old-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <a href="index.php" class="footer-logo">TourBooking</a>
                <p class="opacity-75 small">ພວກເຮົາຄືຜູ້ນຳດ້ານການທ່ອງທ່ຽວໃນລາວ ທີ່ເນັ້ນຄຸນນະພາບ ແລະ ຄວາມປະທັບໃຈຂອງລູກຄ້າເປັນຫຼັກ.</p>
            </div>
            <div class="col-md-4">
                <h5 class="fw-bold mb-4">ຕິດຕໍ່ສອບຖາມ</h5>
                <p class="small mb-1"><i class="fas fa-phone-alt me-2 text-primary"></i> 020 55889977</p>
                <p class="small mb-1"><i class="fas fa-envelope me-2 text-primary"></i> info@beeptour.com</p>
                <p class="small mb-1"><i class="fas fa-map-marker-alt me-2 text-primary"></i> ນະຄອນຫຼວງວຽງຈັນ, ສປປ ລາວ</p>
            </div>
            <div class="col-md-4 text-md-end text-center">
                <h5 class="fw-bold mb-4">ຕິດຕາມພວກເຮົາ</h5>
                <div class="d-flex gap-3 justify-content-center justify-content-md-end">
                    <a href="#" class="btn btn-outline-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <hr class="mt-5 opacity-25">
        <p class="text-center mb-0 small opacity-50">© 2026 Tour Booking System. All Rights Reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>