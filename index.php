<?php include 'config/db.php'; 

// 1. ຮັບຄ່າການຄົ້ນຫາ ແລະ ກັ່ນຕອງ
$search_keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$category_filter = isset($_GET['cat']) ? mysqli_real_escape_string($conn, $_GET['cat']) : 'all';

?>
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

        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #ffffff; scroll-behavior: smooth; }

        /* Navbar */
        .navbar { padding: 15px 0; }

        /* Hero Section ເຕັມຈໍ */
        .hero-section {
            height: 80vh;
            min-height: 500px;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=1920&q=80');
            background-size: cover; background-position: center;
            display: flex; align-items: center; justify-content: center;
            color: white; text-align: center; margin-top: -82px;
        }

        .hero-content h1 { font-size: 3.5rem; font-weight: 800; text-shadow: 2px 4px 10px rgba(0,0,0,0.3); }

        /* Search Bar & Filter */
        .search-container { margin-top: -60px; z-index: 20; position: relative; }
        .search-box { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 15px 40px rgba(0,0,0,0.1); }

        .filter-btn {
            border: 1px solid #ddd; background: white; padding: 8px 22px; border-radius: 50px;
            color: #555; text-decoration: none; font-size: 0.9rem; transition: all 0.3s;
            display: inline-block; margin: 5px;
        }
        .filter-btn:hover, .filter-btn.active { background: var(--primary-color); color: white; border-color: var(--primary-color); }

        /* Tour Card */
        .tour-card {
            border: none; border-radius: 25px; transition: all 0.4s ease;
            overflow: hidden; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .tour-card:hover { transform: translateY(-15px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .tour-img { height: 250px; object-fit: cover; }
        .price-tag { font-size: 1.4rem; font-weight: 700; color: var(--accent-color); }
        .info-badge { background: #f0f2f5; padding: 5px 12px; border-radius: 10px; font-size: 0.85rem; color: #555; }

        section { padding: 80px 0; }

        /* --- ແບບເກົ່າ: Footer --- */
        .old-footer { background: #1a1a1a; color: white; padding: 60px 0 30px; }
        .footer-logo { font-size: 1.8rem; font-weight: 700; color: #0d6efd; margin-bottom: 20px; display: block; text-decoration: none; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php"><i class="fas fa-plane-departure me-2"></i>ຈອງທົວ</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto fw-bold">
                <li class="nav-item"><a class="nav-link px-3 active" href="index.php">ໜ້າຫຼັກ</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#tours">ແພັກເກັດທົວ</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="check_status.php">ກວດສອບການຈອງ</a></li>
                <li class="nav-item ms-lg-4"><a href="login.php" class="btn btn-light rounded-pill px-4 text-primary shadow-sm">ສຳລັບເຈົ້າໜ້າທີ່</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-section">
    <div class="container hero-content">
        <h1 class="display-3 fw-bold mb-3">ໄປທ່ຽວໃສດີມື້ນີ້?</h1>
        <p class="fs-5 opacity-90">ຄົ້ນພົບຄວາມມະຫັດສະຈັນຂອງເມືອງລາວ ພ້ອມບໍລິການລະດັບ VIP</p>
    </div>
</header>

<!-- Search & Filter Container -->
<div class="container search-container">
    <div class="search-box border">
        <form action="index.php#tours" method="GET">
            <div class="row g-3">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill px-3"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control border-start-0 rounded-end-pill py-3 shadow-none" 
                               placeholder="ຄົ້ນຫາຊື່ທົວ ຫຼື ສະຖານທີ່..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">ຄົ້ນຫາທົວ</button>
                </div>
            </div>
        </form>

        <div class="text-center mt-4">
            <div class="d-flex flex-wrap justify-content-center">
                <a href="index.php?cat=all#tours" class="filter-btn <?php echo ($category_filter == 'all') ? 'active' : ''; ?>">ທັງໝົດ</a>
                <a href="index.php?cat=ທົວວັດທະນະທຳ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວວັດທະນະທຳ') ? 'active' : ''; ?>">ວັດທະນະທຳ</a>
                <a href="index.php?cat=ທົວຜະຈົນໄພ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວຜະຈົນໄພ') ? 'active' : ''; ?>">ຜະຈົນໄພ</a>
                <a href="index.php?cat=ທົວຄອບຄົວ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວຄອບຄົວ') ? 'active' : ''; ?>">ຄອບຄົວ</a>
                <a href="index.php?cat=ທົວພັກຜ່ອນ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວພັກຜ່ອນ') ? 'active' : ''; ?>">ພັກຜ່ອນ</a>
            </div>
        </div>
    </div>
</div>

<!-- Tour Catalog Section -->
<section id="tours">
    <div class="container">
        <div class="mb-5 text-center">
            <h2 class="fw-bold">ແພັກເກັດທົວທີ່ແນະນຳ</h2>
            <?php if($search_keyword != '' || $category_filter != 'all'): ?>
                <p class="text-primary">ຜົນການຄົ້ນຫາ: <b><?php echo $category_filter != 'all' ? $category_filter : ''; ?> <?php echo $search_keyword; ?></b></p>
            <?php endif; ?>
        </div>
        
        <div class="row g-4">
            <?php
            $sql = "SELECT * FROM tours WHERE status = 'Active'";
            if ($category_filter != 'all') { $sql .= " AND category = '$category_filter'"; }
            if ($search_keyword != '') { $sql .= " AND (tour_name LIKE '%$search_keyword%' OR highlights LIKE '%$search_keyword%')"; }
            $sql .= " ORDER BY tour_id DESC";
            $result = mysqli_query($conn, $sql);

            if(mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)):
                    $tid = $row['tour_id'];
                    $booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
                    $remaining = $row['max_seats'] - ($booked_res['total'] ?? 0);
                    $rating_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE tour_id = $tid AND status = 'Approved'"));
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card tour-card h-100 bg-white">
                    <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="card-img-top tour-img">
                    <div class="card-body p-4">
                        <div class="mb-2">
                            <?php if($rating_res['count'] > 0): ?>
                                <span class="text-warning small"><i class="fas fa-star"></i> <?php echo round($rating_res['avg_rating'], 1); ?>/5</span>
                            <?php else: ?>
                                <small class="text-muted small">ຍັງບໍ່ມີຄະແນນ</small>
                            <?php endif; ?>
                        </div>
                        <h4 class="card-title fw-bold text-dark mb-3"><?php echo $row['tour_name']; ?></h4>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="info-badge small"><i class="far fa-clock me-1 text-primary"></i> <?php echo $row['duration']; ?></span>
                            <span class="info-badge small"><i class="fas fa-chair me-1 text-success"></i> ຫວ່າງ <?php echo $remaining; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-4">
                            <span class="price-tag"><?php echo number_format($row['price']); ?> <small style="font-size:0.8rem">ກີບ</small></span>
                            <div class="d-flex gap-1">
                                <button class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modal<?php echo $tid; ?>">ຂໍ້ມູນ</button>
                                <a href="booking_form.php?tour_id=<?php echo $tid; ?>" class="btn btn-primary rounded-pill px-4 shadow">ຈອງ</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: ຂໍ້ມູນລະອຽດ -->
            <div class="modal fade" id="modal<?php echo $tid; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content rounded-5 border-0 overflow-hidden">
                        <div class="modal-body p-0">
                            <div class="row g-0">
                                <div class="col-lg-7 bg-dark">
                                    <?php $gal_res = mysqli_query($conn, "SELECT * FROM tour_images WHERE tour_id = $tid"); ?>
                                    <div id="carousel<?php echo $tid; ?>" class="carousel slide h-100" data-bs-ride="carousel">
                                        <div class="carousel-inner h-100">
                                            <div class="carousel-item active h-100">
                                                <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="d-block w-100 h-100" style="object-fit: cover; min-height: 500px;">
                                            </div>
                                            <?php while($gal = mysqli_fetch_assoc($gal_res)): ?>
                                            <div class="carousel-item h-100">
                                                <img src="assets/uploads/tours/<?php echo $gal['image_name']; ?>" class="d-block w-100 h-100" style="object-fit: cover; min-height: 500px;">
                                            </div>
                                            <?php endwhile; ?>
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?php echo $tid; ?>" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carousel<?php echo $tid; ?>" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                                    </div>
                                </div>
                                <div class="col-lg-5 p-4 p-lg-5 bg-white overflow-auto" style="max-height: 90vh;">
                                    <button type="button" class="btn-close float-end shadow-none" data-bs-dismiss="modal"></button>
                                    <span class="badge bg-primary mb-2"><?php echo $row['category']; ?></span>
                                    <h2 class="fw-bold mb-1"><?php echo $row['tour_name']; ?></h2>
                                    <p class="text-muted small mb-4">ລະຫັດ: <?php echo $row['tour_code']; ?> | ນັດພົບ: <?php echo $row['meeting_point']; ?></p>
                                    <h6 class="fw-bold text-primary mb-2">ຈຸດເດັ່ນ:</h6>
                                    <p class="small text-muted mb-4" style="white-space: pre-line;"><?php echo $row['highlights']; ?></p>
                                    <h6 class="fw-bold mb-2 small">ແຜນການເດີນທາງ:</h6>
                                    <div class="bg-light p-3 rounded-4 small text-muted mb-4" style="white-space: pre-line;"><?php echo $row['itinerary']; ?></div>
                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <h3 class="fw-bold text-danger mb-0"><?php echo number_format($row['price']); ?> ກີບ</h3>
                                        <a href="booking_form.php?tour_id=<?php echo $tid; ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow">ຈອງຕອນນີ້</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
                <div class="col-12 text-center py-5 text-muted">ບໍ່ພົບຂໍ້ມູນແພັກເກັດທົວ</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Tracking Section -->
<section id="status" class="bg-primary text-white py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">ຕິດຕາມການຈອງຂອງທ່ານ</h2>
        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <form action="check_status.php" method="GET" class="input-group input-group-lg shadow rounded-pill overflow-hidden border-0">
                    <input type="text" name="phone" class="form-control border-0 px-4 shadow-none" placeholder="ປ້ອນເບີໂທລະສັບ..." required>
                    <button class="btn btn-warning px-5 fw-bold" type="submit">ຄົ້ນຫາ</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- --- ແບບເກົ່າ: Footer Section --- -->
<footer class="old-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <a href="index.php" class="footer-logo">TourBooking</a>
                <p class="opacity-75 small">ພວກເຮົາຄືຜູ້ນຳດ້ານການທ່ອງທ່ຽວໃນລາວ ທີ່ເນັ້ນຄຸນນະພາບ ແລະ ຄວາມປະທັບໃຈຂອງລູກຄ້າເປັນຫຼັກ.</p>
            </div>
            <div class="col-md-4">
                <h5 class="fw-bold mb-4">ຕິດຕໍ່ສອບຖາມ</h5>
                <p class="small mb-2"><i class="fas fa-phone-alt me-2 text-primary"></i> 020 55889977</p>
                <p class="small mb-2"><i class="fas fa-envelope me-2 text-primary"></i> info@beeptour.com</p>
                <p class="small mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i> ນະຄອນຫຼວງວຽງຈັນ, ສປປ ລາວ</p>
            </div>
            <div class="col-md-4 text-md-end text-center">
                <h5 class="fw-bold mb-4">ຕິດຕາມພວກເຮົາ</h5>
                <div class="d-flex gap-3 justify-content-center justify-content-md-end">
                    <a href="#" class="btn btn-outline-light rounded-circle shadow-sm" style="width: 45px; height: 45px; line-height: 32px;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-light rounded-circle shadow-sm" style="width: 45px; height: 45px; line-height: 32px;"><i class="fab fa-whatsapp"></i></a>
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