<?php include 'config/db.php'; 
/** @var mysqli $conn */
/** @var array $lang */ 

$search_keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$category_filter = isset($_GET['cat']) ? mysqli_real_escape_string($conn, $_GET['cat']) : 'all';
?>
<!DOCTYPE html>
<html lang="<?php echo ($current_lang == 'lao') ? 'lo' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Booking - ເຮັດໃຫ້ການເດີນທາງຂອງທ່ານເປັນເລື່ອງງ່າຍ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        /* ແຕ່ງປ້າຍຄຳວ່າ "ເລີ່ມ" */
        .start-label {
            background-color: #0061ff;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            padding: 2px 8px;
            font-family: 'Noto Sans Lao', sans-serif;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        /* ປັບແຕ່ງຫາງລູກສອນຂອງປ້າຍ */
        .leaflet-tooltip-top:before {
            border-top-color: #0061ff;
        }
    </style>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap');
        
        :root {
            --primary: #0061ff;
            --primary-dark: #0046cc;
            --accent: #ff4757;
            --bg-light: #f8faff;
            --text-dark: #2d3436;
            --text-muted: #636e72;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --hover-shadow: 0 20px 40px rgba(0, 97, 255, 0.15);
        }

        body { font-family: 'Noto Sans Lao', sans-serif; background-color: var(--bg-light); color: var(--text-dark); }

        /* Modern Navbar */
        .navbar { background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(10px); padding: 18px 0; box-shadow: 0 2px 15px rgba(0,0,0,0.05); }
        .navbar-brand { font-size: 1.6rem; letter-spacing: -1px; color: var(--primary) !important; }
        .nav-link { color: var(--text-dark) !important; font-weight: 500; transition: 0.3s; margin: 0 10px; }
        .nav-link:hover, .nav-link.active { color: var(--primary) !important; }

        /* Language Switcher */
        .lang-box { background: #eee; padding: 4px; border-radius: 50px; display: flex; gap: 2px; }
        .lang-btn { font-size: 0.7rem; padding: 6px 15px; border-radius: 50px; text-decoration: none; color: #666; font-weight: 700; transition: 0.3s; }
        .lang-btn.active { background: white; color: var(--primary); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }

        /* Hero Section */
        .hero-section {
            height: 65vh; min-height: 500px;
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=1920&q=80');
            background-size: cover; background-position: center;
            display: flex; align-items: center; justify-content: center; text-align: center; color: white;
        }
        .hero-title { font-size: 3.5rem; font-weight: 700; text-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 15px; }

        /* Floating Search Card */
        .search-container { margin-top: -80px; position: relative; z-index: 100; }
        .search-card { background: white; border-radius: 30px; padding: 35px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); border: none; }
        .search-input { height: 55px; border-radius: 15px !important; border: 1px solid #eee; background: #f9f9f9; padding-left: 20px; font-size: 0.95rem; }
        .search-btn { height: 55px; border-radius: 15px; background: var(--primary); font-weight: 700; font-size: 1.05rem; transition: 0.3s; }
        .search-btn:hover { background: var(--primary-dark); transform: translateY(-2px); }

        /* Category Chips */
        .cat-chip { 
            border: 1px solid #eee; background: white; padding: 10px 25px; border-radius: 50px; 
            color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 500; 
            transition: 0.3s; margin: 5px; display: inline-block;
        }
        .cat-chip:hover, .cat-chip.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 8px 20px rgba(0,97,255,0.2); }

        /* Tour Cards Grid */
        .tour-card {
            border: none; border-radius: 28px; background: white; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden; box-shadow: var(--card-shadow); height: 100%; position: relative;
        }
        .tour-card:hover { transform: translateY(-15px); box-shadow: var(--hover-shadow); }
        .tour-img-box { height: 240px; overflow: hidden; position: relative; }
        .tour-img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s; }
        .tour-card:hover .tour-img { scale: 1.1; }
        .price-badge { position: absolute; bottom: 15px; right: 15px; background: white; padding: 8px 18px; border-radius: 50px; font-weight: 700; color: var(--accent); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

        /* Modal Decoration */
        .modal-content { border-radius: 35px; border: none; }
        .carousel-item img { border-radius: 25px; }
        .timeline-item { position: relative; padding-left: 30px; border-left: 2px dashed #ddd; margin-left: 10px; padding-bottom: 25px; }
        .timeline-item::before { content: ''; position: absolute; left: -7px; top: 0; width: 12px; height: 12px; background: var(--primary); border-radius: 50%; border: 3px solid white; box-shadow: 0 0 0 3px rgba(0,97,255,0.1); }
        
        /* Sticky Footer in Modal */
        .booking-bar { background: white; border-top: 1px solid #f1f3f7; padding: 25px 40px; border-bottom-left-radius: 35px; border-bottom-right-radius: 35px; }

        .section-title-line { position: relative; padding-left: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; }
        .section-title-line::before { content: ''; position: absolute; left: 0; top: 5px; height: 20px; width: 5px; background: var(--primary); border-radius: 10px; }

        footer { background: #1a1c23; color: white; padding: 80px 0 30px; }
        .footer-link { color: #a0a0a0; text-decoration: none; transition: 0.3s; font-size: 0.9rem; }
        .footer-link:hover { color: white; padding-left: 5px; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-paper-plane me-2"></i>TourBooking</a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto fw-bold align-items-center">
                <li class="nav-item"><a class="nav-link px-3 active" href="index.php">ໜ້າຫຼັກ</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="check_status.php">ຕິດຕາມການຈອງ</a></li>
                <li class="nav-item ms-lg-3">
                    <div class="lang-box">
                        <a href="?lang=lao" class="lang-btn <?php echo ($current_lang == 'lao') ? 'active' : ''; ?>">LAO</a>
                        <a href="?lang=eng" class="lang-btn <?php echo ($current_lang == 'eng') ? 'active' : ''; ?>">ENG</a>
                    </div>
                </li>
                <li class="nav-item ms-lg-3"><a href="login.php" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm small fw-bold">ເຂົ້າສູ່ລະບົບ</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-section">
    <div class="container">
        <h1 class="hero-title"><?php echo $lang['hero_title']; ?></h1>
        <p class="fs-4 opacity-75 fw-light">ຄົ້ນພົບປະສົບການການທ່ອງທ່ຽວທີ່ເໜືອລະດັບ ພ້ອມບໍລິການທີ່ດີທີ່ສຸດ</p>
    </div>
</header>

<!-- Search Area -->
<div class="container search-container">
    <div class="search-card">
        <form action="index.php#tours" method="GET">
            <div class="row g-3">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 px-3"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control search-input shadow-none" placeholder="ຄົ້ນຫາຈຸດໝາຍປາຍທາງ ຫຼື ແພັກເກັດທົວ..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 search-btn shadow">ຄົ້ນຫາທົວ</button>
                </div>
            </div>
        </form>
        <div class="text-center mt-4">
            <a href="index.php?cat=all#tours" class="cat-chip <?php echo ($category_filter == 'all') ? 'active' : ''; ?>">ທຸກແພັກເກັດ</a>
            <a href="index.php?cat=ທົວວັດທະນະທຳ#tours" class="cat-chip <?php echo ($category_filter == 'ທົວວັດທະນະທຳ') ? 'active' : ''; ?>">ວັດທະນະທຳ</a>
            <a href="index.php?cat=ທົວຜະຈົນໄພ#tours" class="cat-chip <?php echo ($category_filter == 'ທົວຜະຈົນໄພ') ? 'active' : ''; ?>">ຜະຈົນໄພ</a>
            <a href="index.php?cat=ທົວພັກຜ່ອນ#tours" class="cat-chip <?php echo ($category_filter == 'ທົວພັກຜ່ອນ') ? 'active' : ''; ?>">ພັກຜ່ອນ</a>
        </div>
    </div>
</div>

<!-- Tour Listing -->
<div class="container my-5 pt-4" id="tours">
    <h3 class="fw-bold mb-4"><i class="fas fa-star text-warning me-2"></i>ແພັກເກັດທົວແນະນຳ</h3>
    <div class="row g-4">
        <?php
        $sql = "SELECT * FROM tours WHERE status = 'Active' AND start_date >= CURDATE()";
        if ($category_filter != 'all') $sql .= " AND category = '$category_filter'";
        if ($search_keyword != '') $sql .= " AND (tour_name LIKE '%$search_keyword%')";
        $sql .= " ORDER BY start_date ASC";
        
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_assoc($result)):
            $tid = $row['tour_id'];
            
            // ດຶງຈຳນວນຄົນຈອງ
            $booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
            $booked_count = $booked_res['total'] ?? 0;
            $remaining = $row['max_seats'] - $booked_count;

            // ດຶງຮູບ Gallery (ເອົາໄວ້ບ່ອນນີ້ບ່ອນດຽວພໍ)
            $gallery_res = mysqli_query($conn, "SELECT image_name FROM tour_images WHERE tour_id = '$tid'");
            $gallery_images = [];
            while($g_row = mysqli_fetch_assoc($gallery_res)) { $gallery_images[] = $g_row['image_name']; }
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="tour-card shadow-sm">
                <div class="tour-img-box">
                    <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="tour-img">
                    <div class="price-badge"><?php echo number_format($row['price']); ?> ກີບ</div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 small mb-2"><?php echo $row['category']; ?></span>
                        <div class="small text-muted"><i class="fas fa-users me-1"></i>ວ່າງ <?php echo $remaining; ?></div>
                    </div>
                    <h5 class="fw-bold text-dark mb-3"><?php echo $row['tour_name']; ?></h5>
                    <div class="small text-muted mb-3">
                        <div class="mb-1"><i class="far fa-calendar-alt me-2 text-primary"></i>ວັນທີ: <b><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></b></div>
                        <div><i class="fas fa-map-marker-alt me-2 text-primary"></i>ຈຸດນັດພົບ: <?php echo $row['meeting_point']; ?></div>
                    </div>
                    <button class="btn btn-outline-primary w-100 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#modal<?php echo $tid; ?>">ເບິ່ງລາຍລະອຽດ</button>
                </div>
            </div>
        </div>

        <!-- Modern Modal -->
        <div class="modal fade" id="modal<?php echo $tid; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content rounded-5 border-0 shadow-lg overflow-hidden position-relative">
                    
                    <!-- 1. ປຸ່ມປິດແບບລອຍ (ວາງໄວ້ບ່ອນນີ້ ເພື່ອໃຫ້ມັນຢູ່ເທິງສຸດສະເໝີ) -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-4 shadow-none" 
                            data-bs-dismiss="modal" aria-label="Close" 
                            style="z-index: 9999; background-color: rgba(255,255,255,0.8); border-radius: 50%; padding: 10px;">
                    </button>

                    <div class="modal-body p-0">
                        <div class="row g-0">
                            <!-- ເນື້ອໃນ Modal ຂອງເຈົ້າ... -->
                            <!-- Left: Slides & Basic Info -->
                            <div class="col-lg-5 bg-light p-4" style="max-height: 90vh; overflow-y: auto;">
                                <!-- ສ່ວນສະແດງຮູບພາບ (Carousel) ທີ່ປັບປຸງໃໝ່ -->
                                <div id="carousel<?php echo $tid; ?>" class="carousel slide mb-4 shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel">
                                    <!-- ຈຸດບອກຈຳນວນຮູບ -->
                                    <div class="carousel-indicators">
                                        <button type="button" data-bs-target="#carousel<?php echo $tid; ?>" data-bs-slide-to="0" class="active"></button>
                                        <?php foreach($gallery_images as $index => $img): ?>
                                            <button type="button" data-bs-target="#carousel<?php echo $tid; ?>" data-bs-slide-to="<?php echo $index + 1; ?>"></button>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="d-block w-100" style="height: 350px; object-fit: cover;">
                                        </div>
                                        <?php foreach($gallery_images as $img): ?>
                                            <div class="carousel-item">
                                                <img src="assets/uploads/tours/<?php echo $img; ?>" class="d-block w-100" style="height: 350px; object-fit: cover;">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- ປຸ່ມກົດ (ເພີ່ມ Background ສີດຳຈາງໆ ໃຫ້ເຫັນປຸ່ມຊັດເຈນ) -->
                                    <?php if(count($gallery_images) > 0): ?>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?php echo $tid; ?>" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon p-3 bg-dark rounded-circle" aria-hidden="true" style="width:20px; height:20px;"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carousel<?php echo $tid; ?>" data-bs-slide="next">
                                            <span class="carousel-control-next-icon p-3 bg-dark rounded-circle" aria-hidden="true" style="width:20px; height:20px;"></span>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <h4 class="fw-bold text-dark mb-4"><?php echo $row['tour_name']; ?></h4>
                                
                                <div class="p-3 bg-white rounded-4 shadow-sm mb-4 small">
                                    <div class="section-title-line">ລາຍລະອຽດເບື້ອງຕົ້ນ</div>
                                    <div class="mb-2"><b>ໝວດໝູ່:</b> <?php echo $row['category']; ?></div>
                                    <div class="mb-2"><b>ໄລຍະເວລາ:</b> <?php echo $row['duration']; ?></div>
                                    <div><b>ຈຸດນັດພົບ:</b> <?php echo $row['meeting_point']; ?></div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12"><div class="p-3 rounded-4 border-start border-4 border-success bg-white shadow-sm small"><b>ສິ່ງທີ່ລວມຢູ່ນຳ:</b><br><?php echo nl2br($row['whats_included']); ?></div></div>
                                    <div class="col-12"><div class="p-3 rounded-4 border-start border-4 border-danger bg-white shadow-sm small"><b>ບໍ່ລວມ:</b><br><?php echo nl2br($row['whats_excluded']); ?></div></div>
                                </div>
                            </div>
                            
                            <!-- Right: Map & Itinerary -->
                            <div class="col-lg-7 bg-white d-flex flex-column" style="max-height: 90vh;">
                                <div class="p-4 p-lg-5 flex-grow-1 overflow-auto">
                                    <!-- ລຶບປຸ່ມ btn-close ບ່ອນນີ້ອອກແລ້ວ -->
                                    <h6 class="section-title-line">ແຜນທີ່ເສັ້ນທາງ</h6>
                                    <div id="map-<?php echo $tid; ?>" class="shadow-sm border mb-5" style="height: 280px; border-radius: 20px; z-index:1"></div>
                                    
                                    <h6 class="section-title-line">ແຜນການເດີນທາງລະອຽດ</h6>
                                    <?php $itinerary = json_decode($row['itinerary'], true);
                                    if($itinerary) foreach($itinerary as $day): ?>
                                        <div class="mb-4 mt-3">
                                            <div class="badge bg-primary rounded-pill mb-3 px-3 py-2">ມື້ທີ <?php echo $day['day']; ?></div>
                                            <?php foreach($day['events'] as $ev): ?>
                                                <div class="timeline-item">
                                                    <div class="fw-bold text-dark mb-1 small"><?php echo $ev['location']; ?></div>
                                                    <div class="text-muted small" style="font-size: 0.75rem;"><?php echo $ev['desc']; ?></div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Sticky Footer in Modal -->
                                <div class="booking-bar d-flex justify-content-between align-items-center shadow-lg">
                                    <div>
                                        <small class="text-muted d-block fw-bold mb-1">ລາຄາຕໍ່ທ່ານ</small>
                                        <span class="fw-bold text-danger fs-3"><?php echo number_format($row['price']); ?> <small class="fs-6 fw-normal">ກີບ/LAK</small></span>
                                    </div>
                                    <a href="booking_form.php?tour_id=<?php echo $tid; ?>" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow">ຈອງຕອນນີ້</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            (function() {
                const modalId = 'modal<?php echo $tid; ?>';
                const mapId = 'map-<?php echo $tid; ?>';
                const itiData = <?php echo $row['itinerary'] ?: '[]'; ?>;

                document.getElementById(modalId).addEventListener('shown.bs.modal', function () {
                    const map = L.map(mapId).setView([17.9757, 102.6331], 11);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                    
                    let allPoints = [];
                    let isFirst = true; // ✅ ສ້າງຕົວແປໄວ້ເຊັກຈຸດທຳອິດ

                    itiData.forEach((day) => {
                        day.events.forEach(ev => {
                            if (ev.lat && ev.lng) {
                                const pos = [parseFloat(ev.lat), parseFloat(ev.lng)];
                                allPoints.push(pos); 
                                
                                const marker = L.marker(pos).addTo(map).bindPopup(ev.location);

                                // ✅ ຖ້າແມ່ນຈຸດທຳອິດ ໃຫ້ຂຽນວ່າ "ເລີ່ມ"
                                if (isFirst) {
                                    marker.bindTooltip("ເລີ່ມ", { 
                                        permanent: true,   // ໃຫ້ຂໍ້ຄວາມໂຊຄ້າງໄວ້ຕະຫຼອດ
                                        direction: 'top', // ໃຫ້ຢູ່ເທິງໝຸດ
                                        className: 'start-label' // ຊື່ class ສຳລັບແຕ່ງສີ
                                    }).openTooltip();
                                    isFirst = false; // ປ່ຽນຄ່າເປັນ false ເພື່ອບໍ່ໃຫ້ໝຸດຕໍ່ໄປຂຶ້ນຂໍ້ຄວາມນຳ
                                }
                            }
                        });
                    });

                    if (allPoints.length > 1) {
                        L.polyline(allPoints, {
                            color: '#0061ff',
                            weight: 4,
                            opacity: 0.6,
                            dashArray: '10, 10',
                            lineJoin: 'round'
                        }).addTo(map);

                        map.fitBounds(L.polyline(allPoints).getBounds(), { padding: [40, 40] });
                    }
                    
                    map.invalidateSize(); 
                }, { once: true });
            })();
            </script>
        <?php endwhile; ?>
    </div>
</div>

<!-- Footer -->
<footer class="mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4 pe-md-5">
                <h4 class="fw-bold mb-4"><i class="fas fa-paper-plane me-2"></i>TourBooking</h4>
                <p class="text-muted small lh-lg">ພວກເຮົາເປັນຜູ້ຊ່ຽວຊານດ້ານການຈັດທົວທ່ອງທ່ຽວໃນລາວ ທີ່ເນັ້ນຄວາມສະດວກສະບາຍ ແລະ ປະສົບການທີ່ດີທີ່ສຸດໃຫ້ແກ່ລູກຄ້າ.</p>
            </div>
            <div class="col-md-2">
                <h6 class="fw-bold mb-4">ເມນູຫຼັກ</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="index.php" class="footer-link">ໜ້າຫຼັກ</a>
                    <a href="check_status.php" class="footer-link">ກວດສອບການຈອງ</a>
                    <a href="#" class="footer-link">ຣີວິວຈາກລູກຄ້າ</a>
                </div>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold mb-4">ຕິດຕໍ່ພວກເຮົາ</h6>
                <div class="text-muted small d-flex flex-column gap-3">
                    <div><i class="fas fa-phone-alt me-2 text-primary"></i> 020 55889977</div>
                    <div><i class="fas fa-envelope me-2 text-primary"></i> info@tourbooking.com</div>
                    <div><i class="fas fa-map-marker-alt me-2 text-primary"></i> ບ້ານ ໂພນໄຊ, ເມືອງ ໄຊເສດຖາ, ນະຄອນຫຼວງວຽງຈັນ</div>
                </div>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold mb-4">ຕິດຕາມພວກເຮົາ</h6>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-outline-light rounded-circle shadow-sm" style="width:40px;height:40px;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-light rounded-circle shadow-sm" style="width:40px;height:40px;"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-5 opacity-10">
        <p class="text-center text-muted small mb-0">© 2026 Tour Booking System. All Rights Reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>