<?php include 'config/db.php'; 

// 1. ຮັບຄ່າການຄົ້ນຫາ ແລະ ກັ່ນຕອງ
$search_keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$category_filter = isset($_GET['cat']) ? mysqli_real_escape_string($conn, $_GET['cat']) : 'all';

?>
<!DOCTYPE html>
<html lang="<?php echo ($current_lang == 'lao') ? 'lo' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Booking - <?php echo $lang['hero_title']; ?></title>
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

        /* Hero Section */
        .hero-section {
            height: 75vh;
            min-height: 500px;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=1920&q=80');
            background-size: cover; background-position: center;
            display: flex; align-items: center; justify-content: center;
            color: white; text-align: center; margin-top: -82px;
        }

        .hero-content h1 { font-size: clamp(2rem, 8vw, 4rem); font-weight: 800; text-shadow: 2px 4px 10px rgba(0,0,0,0.3); }

        /* Search & Filter Bar */
        .search-container { margin-top: -60px; z-index: 20; position: relative; }
        .search-box { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 15px 40px rgba(0,0,0,0.15); border: 1px solid #eee; }

        .filter-btn {
            border: 1px solid #ddd; background: white; padding: 8px 22px; border-radius: 50px;
            color: #555; text-decoration: none; font-size: 0.9rem; transition: all 0.3s;
            display: inline-block; margin: 5px;
        }
        .filter-btn:hover, .filter-btn.active { background: var(--primary-color); color: white; border-color: var(--primary-color); box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3); }

        /* Language Switcher Buttons */
        .lang-btn {
            font-size: 0.75rem; padding: 5px 12px; border-radius: 50px; text-decoration: none;
            border: 1px solid rgba(255,255,255,0.4); color: white; transition: 0.3s;
            font-weight: bold;
        }
        .lang-btn.active { background: white; color: var(--primary-color); border-color: white; }
        .lang-btn:hover:not(.active) { background: rgba(255,255,255,0.2); color: white; }

        /* Tour Card */
        .tour-card {
            border: none; border-radius: 25px; transition: all 0.4s ease;
            overflow: hidden; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .tour-card:hover { transform: translateY(-15px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .tour-img { height: 250px; object-fit: cover; }
        .price-tag { font-size: 1.4rem; font-weight: 700; color: var(--accent-color); }
        .info-badge { background: #f0f2f5; padding: 5px 12px; border-radius: 10px; font-size: 0.85rem; color: #555; display: inline-block; width: 100%; }

        section { padding: 90px 0; }
        .section-title { font-weight: 700; margin-bottom: 50px; position: relative; padding-bottom: 15px; }
        .section-title::after { content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 4px; background: var(--primary-color); border-radius: 2px; }

        .feature-box { padding: 30px; border-radius: 20px; background: #f8f9fa; height: 100%; transition: 0.3s; border: 1px solid #f0f0f0; }
        .feature-box:hover { background: #e7f1ff; transform: translateY(-5px); }

        .old-footer { background: #1a1a1a; color: white; padding: 60px 0 30px; }
        .footer-logo { font-size: 1.8rem; font-weight: 700; color: #0d6efd; text-decoration: none; }
        
        .blink { animation: blinker 1.5s linear infinite; }
        @keyframes blinker { 50% { opacity: 0.3; } }
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
                <li class="nav-item"><a class="nav-link px-3" href="index.php"><?php echo $lang['nav_home']; ?></a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#tours"><?php echo $lang['nav_tours']; ?></a></li>
                <li class="nav-item"><a class="nav-link px-3" href="check_status.php"><?php echo $lang['nav_status']; ?></a></li>
                
                <!-- ປຸ່ມປ່ຽນພາສາ -->
                <li class="nav-item d-flex gap-1 ms-lg-3 my-2 my-lg-0 p-1 bg-dark bg-opacity-25 rounded-pill">
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
    <div class="container hero-content">
        <h1 class="display-3 fw-bold mb-3"><?php echo $lang['hero_title']; ?></h1>
        <p class="fs-5 opacity-90"><?php echo $lang['hero_subtitle']; ?></p>
        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="#tours" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg"><?php echo $lang['btn_start']; ?></a>
        </div>
    </div>
</header>

<!-- Search & Filter Container -->
<div class="container search-container">
    <div class="search-box">
        <form action="index.php#tours" method="GET">
            <div class="row g-3">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill px-3"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control border-start-0 rounded-end-pill py-3 shadow-none" 
                               placeholder="<?php echo ($current_lang=='lao')?'ຄົ້ນຫາຊື່ທົວ ຫຼື ສະຖານທີ່...':'Search tours or locations...'; ?>" value="<?php echo htmlspecialchars($search_keyword); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">
                        <i class="fas fa-search me-2"></i> <?php echo ($current_lang=='lao')?'ຄົ້ນຫາ':'Search'; ?>
                    </button>
                </div>
            </div>
        </form>

        <div class="text-center mt-4">
            <div class="d-flex flex-wrap justify-content-center">
                <a href="index.php?cat=all#tours" class="filter-btn <?php echo ($category_filter == 'all') ? 'active' : ''; ?>">
                    <?php echo ($current_lang=='lao')?'ທັງໝົດ':'All'; ?>
                </a>
                <a href="index.php?cat=ທົວວັດທະນະທຳ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວວັດທະນະທຳ') ? 'active' : ''; ?>">
                    <?php echo ($current_lang=='lao')?'ວັດທະນະທຳ':'Cultural'; ?>
                </a>
                <a href="index.php?cat=ທົວຜະຈົນໄພ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວຜະຈົນໄພ') ? 'active' : ''; ?>">
                    <?php echo ($current_lang=='lao')?'ຜະຈົນໄພ':'Adventure'; ?>
                </a>
                <a href="index.php?cat=ທົວຄອບຄົວ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວຄອບຄົວ') ? 'active' : ''; ?>">
                    <?php echo ($current_lang=='lao')?'ຄອບຄົວ':'Family'; ?>
                </a>
                <a href="index.php?cat=ທົວພັກຜ່ອນ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວພັກຜ່ອນ') ? 'active' : ''; ?>">
                    <?php echo ($current_lang=='lao')?'ພັກຜ່ອນ':'Leisure'; ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Why Choose Us -->
<section class="section-padding bg-white">
    <div class="container text-center">
        <h2 class="section-title"><?php echo $lang['why_title']; ?></h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box shadow-sm">
                    <i class="fas fa-tags fa-3x text-primary mb-4"></i>
                    <h4 class="fw-bold"><?php echo $lang['why_1_title']; ?></h4>
                    <p class="text-muted small"><?php echo $lang['why_1_desc']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box shadow-sm">
                    <i class="fas fa-shield-alt fa-3x text-success mb-4"></i>
                    <h4 class="fw-bold"><?php echo $lang['why_2_title']; ?></h4>
                    <p class="text-muted small"><?php echo $lang['why_2_desc']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box shadow-sm">
                    <i class="fas fa-headset fa-3x text-warning mb-4"></i>
                    <h4 class="fw-bold"><?php echo $lang['why_3_title']; ?></h4>
                    <p class="text-muted small"><?php echo $lang['why_3_desc']; ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tour Catalog Section -->
<section id="tours" style="background-color: #f0f2f5;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title"><?php echo $lang['tour_recommend']; ?></h2>
            <?php if($search_keyword != '' || $category_filter != 'all'): ?>
                <p class="text-primary mt-n4 mb-5"><?php echo ($current_lang=='lao')?'ຜົນການຄົ້ນຫາ':'Search results for'; ?>: <b><?php echo $category_filter != 'all' ? $category_filter : ''; ?> <?php echo $search_keyword; ?></b></p>
            <?php endif; ?>
        </div>
        
        <div class="row g-4">
            <?php
            $sql = "SELECT * FROM tours WHERE status = 'Active'";
            if ($category_filter != 'all') { $sql .= " AND category = '$category_filter'"; }
            if ($search_keyword != '') { $sql .= " AND (tour_name LIKE '%$search_keyword%' OR highlights LIKE '%$search_keyword%' OR tour_code LIKE '%$search_keyword%')"; }
            $sql .= " ORDER BY tour_id DESC";
            $result = mysqli_query($conn, $sql);

            if(mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)):
                    $tid = $row['tour_id'];
                    $booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
                    $remaining = $row['max_seats'] - ($booked_res['total'] ?? 0);
                    
                    // ຄະແນນດາວ
                    $rating_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg_r, COUNT(*) as count FROM reviews WHERE tour_id = $tid AND status = 'Approved'"));
                    $avg = round($rating_res['avg_r'], 1);
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card tour-card h-100 bg-white">
                    <div class="position-relative">
                        <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="card-img-top tour-img">
                        <span class="position-absolute top-0 start-0 bg-primary text-white px-3 py-1 m-3 rounded-pill small fw-bold shadow-sm">
                            <?php echo $row['category']; ?>
                        </span>
                        <?php if($remaining <= 3 && $remaining > 0): ?>
                            <span class="position-absolute top-0 end-0 bg-danger text-white px-3 py-1 m-3 rounded-pill fw-bold blink shadow-sm" style="font-size: 0.7rem;">
                                <?php echo ($current_lang=='lao')?'ໃກ້ເຕັມ!':'Almost Full!'; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="mb-2">
                            <?php if($rating_res['count'] > 0): ?>
                                <span class="text-warning small"><i class="fas fa-star"></i> <?php echo $avg; ?>/5</span>
                                <small class="text-muted ms-1">(<?php echo $rating_res['count']; ?>)</small>
                            <?php else: ?>
                                <small class="text-muted small italic"><?php echo ($current_lang=='lao')?'ຍັງບໍ່ມີຄະແນນ':'No ratings'; ?></small>
                            <?php endif; ?>
                        </div>

                        <h4 class="card-title fw-bold text-dark mb-3"><?php echo $row['tour_name']; ?></h4>
                        
                        <div class="row g-2 mb-4">
                            <div class="col-6"><span class="info-badge"><i class="far fa-clock me-1 text-primary"></i> <?php echo $row['duration']; ?></span></div>
                            <div class="col-6"><span class="info-badge <?php echo ($remaining <= 2)?'text-danger fw-bold':''; ?>"><i class="fas fa-chair me-1"></i> <?php echo $remaining; ?> <?php echo ($current_lang=='lao')?'ບ່ອນ':'Seats'; ?></span></div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-4">
                            <div>
                                <small class="text-muted d-block small"><?php echo ($current_lang=='lao')?'ລາຄາ/ທ່ານ':'Price/Pax'; ?></small>
                                <span class="price-tag"><?php echo number_format($row['price']); ?></span>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modal<?php echo $tid; ?>"><?php echo $lang['tour_btn_detail']; ?></button>
                                <?php if($remaining > 0): ?>
                                    <a href="booking_form.php?tour_id=<?php echo $tid; ?>" class="btn btn-primary rounded-pill px-4 shadow"><?php echo $lang['tour_btn_book']; ?></a>
                                <?php else: ?>
                                    <button class="btn btn-secondary rounded-pill px-3" disabled><?php echo $lang['tour_full']; ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modal<?php echo $tid; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content rounded-5 border-0 overflow-hidden shadow-lg">
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
                                    <h2 class="fw-bold mb-1 text-dark"><?php echo $row['tour_name']; ?></h2>
                                    <p class="text-muted small mb-4">ID: <?php echo $row['tour_code']; ?> | <i class="fas fa-map-marker-alt text-danger"></i> <?php echo $row['meeting_point']; ?></p>
                                    
                                    <h6 class="fw-bold text-primary mb-2"><?php echo ($current_lang=='lao')?'จุดเด่น:':'Highlights:'; ?></h6>
                                    <p class="small text-muted mb-4" style="white-space: pre-line;"><?php echo $row['highlights']; ?></p>

                                    <div class="row mb-4">
                                        <div class="col-6"><h6 class="fw-bold text-success small"><?php echo ($current_lang=='lao')?'ສິ່ງທີ່ລວມ:':'Includes:'; ?></h6><p class="small text-muted" style="white-space: pre-line; font-size: 0.7rem;"><?php echo $row['whats_included']; ?></p></div>
                                        <div class="col-6"><h6 class="fw-bold text-danger small"><?php echo ($current_lang=='lao')?'ບໍ່ລວມ:':'Excludes:'; ?></h6><p class="small text-muted" style="white-space: pre-line; font-size: 0.7rem;"><?php echo $row['whats_excluded']; ?></p></div>
                                    </div>

                                    <h6 class="fw-bold mb-2 small"><?php echo ($current_lang=='lao')?'ແຜນການເດີນທາງ:':'Itinerary:'; ?></h6>
                                    <div class="bg-light p-3 rounded-4 small text-muted mb-4" style="white-space: pre-line;"><?php echo $row['itinerary']; ?></div>

                                    <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-4">
                                        <h3 class="fw-bold text-danger mb-0"><?php echo number_format($row['price']); ?> <small class="fs-6">LAK</small></h3>
                                        <a href="booking_form.php?tour_id=<?php echo $tid; ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow"><?php echo $lang['tour_btn_book']; ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
                <div class="col-12 text-center py-5 text-muted"><?php echo ($current_lang=='lao')?'ບໍ່ພົບຂໍ້ມູນແພັກເກັດທົວ':'No tours found'; ?></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Footer Section (Old Style as requested) -->
<footer class="old-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <a href="index.php" class="footer-logo">TourBooking</a>
                <p class="opacity-75 small"><?php echo ($current_lang=='lao')?'ພວກເຮົາຄືຜູ້ນຳດ້ານການທ່ອງທ່ຽວໃນລາວ ທີ່ເນັ້ນຄຸນນະພາບ ແລະ ຄວາມປະທັບໃຈຂອງລູກຄ້າເປັນຫຼັກ.':'Leading tour operator in Laos, focusing on quality and customer satisfaction.'; ?></p>
            </div>
            <div class="col-md-4">
                <h5 class="fw-bold mb-4"><?php echo $lang['footer_contact']; ?></h5>
                <p class="small mb-2"><i class="fas fa-phone-alt me-2 text-primary"></i> 020 55889977</p>
                <p class="small mb-2"><i class="fas fa-envelope me-2 text-primary"></i> info@beeptour.com</p>
                <p class="small mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i> <?php echo ($current_lang=='lao')?'ນະຄອນຫຼວງວຽງຈັນ, ສປປ ລາວ':'Vientiane, Lao PDR'; ?></p>
            </div>
            <div class="col-md-4 text-md-end text-center">
                <h5 class="fw-bold mb-4"><?php echo ($current_lang=='lao')?'ຕິດຕາມພວກເຮົາ':'Follow Us'; ?></h5>
                <div class="d-flex gap-3 justify-content-center justify-content-md-end">
                    <a href="#" class="btn btn-outline-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;"><i class="fab fa-whatsapp"></i></a>
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