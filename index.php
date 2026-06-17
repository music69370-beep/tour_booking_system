<?php include 'config/db.php'; 
/** @var mysqli $conn */
/** @var array $lang */ //
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        :root { --primary-color: #0d6efd; --accent-color: #ff4757; }
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fa; }

        /* Navbar Custom */
        .navbar { padding: 15px 0; background-color: #0d6efd !important; }
        .hero-section {
            height: 50vh; min-height: 350px;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=1920&q=80');
            background-size: cover; background-position: center;
            display: flex; align-items: center; justify-content: center; color: white; text-align: center;
        }

        /* Language Switcher */
        .lang-btn { font-size: 0.75rem; padding: 5px 12px; border-radius: 50px; text-decoration: none; border: 1px solid rgba(255,255,255,0.4); color: white; transition: 0.3s; font-weight: bold; }
        .lang-btn.active { background: white; color: #0d6efd; border-color: white; }

        /* Search & Filter */
        .search-container { margin-top: -60px; z-index: 20; position: relative; }
        .search-box { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 15px 40px rgba(0,0,0,0.1); border: 1px solid #eee; }
        .filter-btn { border: 1px solid #ddd; background: white; padding: 8px 20px; border-radius: 50px; color: #555; text-decoration: none; font-size: 0.9rem; transition: 0.3s; margin: 5px; display: inline-block; }
        .filter-btn:hover, .filter-btn.active { background: var(--primary-color); color: white; border-color: var(--primary-color); }

        /* Tour Cards */
        .tour-card { border: none; border-radius: 25px; transition: 0.4s; overflow: hidden; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.05); height: 100%; display: flex; flex-direction: column; }
        .tour-card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .tour-img { width: 100%; height: 220px; object-fit: cover; }
        
        /* Modal & Timeline */
        .modal-body-content { max-height: 75vh; overflow-y: auto; padding-bottom: 110px; }
        .itinerary-map { height: 320px; width: 100%; border-radius: 20px; margin-bottom: 25px; border: 1px solid #ddd; z-index: 1; }
        .timeline-item { position: relative; padding-left: 25px; border-left: 2px solid #e9ecef; margin-left: 10px; padding-bottom: 15px; }
        .timeline-item::before { content: ''; position: absolute; left: -6px; top: 5px; width: 10px; height: 10px; background: var(--primary-color); border-radius: 50%; }
        
        /* Sticky Footer */
        .sticky-booking-footer { 
            position: absolute; bottom: 0; right: 0; width: 58.3%; 
            background: rgba(255,255,255,0.98); backdrop-filter: blur(10px); 
            padding: 20px 50px; border-top: 1px solid #eee; z-index: 100; border-bottom-right-radius: 25px; 
        }
        @media (max-width: 991px) { .sticky-booking-footer { width: 100%; border-bottom-left-radius: 25px; padding: 15px 30px; } .modal-body-content { max-height: none; } }
        
        .detail-box { background: #f8f9fc; border-radius: 15px; padding: 15px; margin-bottom: 15px; border: 1px solid #eee; }
        .section-header { font-weight: 700; font-size: 0.95rem; margin-bottom: 10px; display: flex; align-items: center; color: #2d3436; }
        .section-header i { color: var(--primary-color); margin-right: 10px; width: 20px; text-align: center; }
    </style>
</head>
<body>

<!-- Navbar ສົມບູນ -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php"><i class="fas fa-plane-departure me-2"></i>TourBooking</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto fw-bold align-items-center">
                <li class="nav-item"><a class="nav-link px-3 active" href="index.php">ໜ້າຫຼັກ</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="check_status.php">ກວດສອບການຈອງ</a></li>
                <!-- 1. ເພີ່ມປຸ່ມ "ຣີວິວທົວ" (ໃຫ້ຄຳຄິດເຫັນ) -->
                <li class="nav-item"><a class="nav-link px-3" href="check_status.php"><i class="fas fa-comment-alt me-1"></i> ຣີວິວທົວ</a></li>
                
                <li class="nav-item d-flex gap-1 ms-lg-3 p-1 bg-dark bg-opacity-25 rounded-pill">
                    <a href="?lang=lao" class="lang-btn <?php echo ($current_lang == 'lao') ? 'active' : ''; ?>">LAO</a>
                    <a href="?lang=eng" class="lang-btn <?php echo ($current_lang == 'eng') ? 'active' : ''; ?>">ENG</a>
                </li>
                <li class="nav-item ms-lg-3"><a href="login.php" class="btn btn-light rounded-pill px-4 text-primary shadow-sm small">ເຂົ້າສູ່ລະບົບ</a></li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero-section">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?php echo $lang['hero_title']; ?></h1>
        <p class="fs-5 opacity-90"><?php echo $lang['hero_subtitle']; ?></p>
    </div>
</header>

<!-- ສ່ວນຄົ້ນຫາ (Search Area) -->
<div class="container search-container" id="search-area">
    <div class="search-box shadow">
        <form action="index.php#tours" method="GET">
            <div class="row g-3">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill px-3"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control border-start-0 rounded-end-pill py-3 shadow-none" placeholder="ຄົ້ນຫາແພັກເກັດ ຫຼື ສະຖານທີ່..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">ຄົ້ນຫາທົວ</button>
                </div>
            </div>
        </form>
        <div class="text-center mt-4">
            <a href="index.php?cat=all#tours" class="filter-btn <?php echo ($category_filter == 'all') ? 'active' : ''; ?>">ທັງໝົດ</a>
            <a href="index.php?cat=ທົວວັດທະນະທຳ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວວັດທະນະທຳ') ? 'active' : ''; ?>">ວັດທະນະທຳ</a>
            <a href="index.php?cat=ທົວຜະຈົນໄພ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວຜະຈົນໄພ') ? 'active' : ''; ?>">ຜະຈົນໄພ</a>
            <a href="index.php?cat=ທົວພັກຜ່ອນ#tours" class="filter-btn <?php echo ($category_filter == 'ທົວພັກຜ່ອນ') ? 'active' : ''; ?>">ພັກຜ່ອນ</a>
        </div>
    </div>
</div>

<div class="container my-5" id="tours">
    <div class="row g-4">
        <?php
        $sql = "SELECT * FROM tours WHERE status = 'Active'";
        if ($category_filter != 'all') $sql .= " AND category = '$category_filter'";
        if ($search_keyword != '') $sql .= " AND (tour_name LIKE '%$search_keyword%')";
        $sql .= " ORDER BY tour_id DESC";
        
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_assoc($result)):
            $tid = $row['tour_id'];
            $booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
            $booked_count = $booked_res['total'] ?? 0;
            $max_seats = $row['max_seats'];
            $remaining = $max_seats - $booked_count;
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="tour-card h-100">
                <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="tour-img">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3"><?php echo $row['tour_name']; ?></h5>
                    <div class="small text-muted mb-2"><i class="far fa-calendar-alt me-2 text-primary"></i>ວັນທີ: <b><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></b></div>
                    <!-- 4. ສະແດງຈຳນວນຄົນ 10 / 15 -->
                    <div class="small text-dark mb-3">
                        <i class="fas fa-users me-2 text-primary"></i>ບ່ອນນັ່ງ: <b><?php echo $booked_count; ?> / <?php echo $max_seats; ?></b>
                        <span class="ms-2 badge <?php echo ($remaining > 0) ? 'bg-success' : 'bg-danger'; ?>"><?php echo ($remaining > 0) ? "ວ່າງ $remaining" : "ເຕັມ"; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-3">
                        <span class="fw-bold text-danger fs-5"><?php echo number_format($row['price']); ?> ກີບ</span>
                        <button class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal<?php echo $tid; ?>">ເບິ່ງຂໍ້ມູນ</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal ລາຍລະອຽດທົວ -->
        <div class="modal fade" id="modal<?php echo $tid; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content rounded-5 border-0 shadow-lg position-relative overflow-hidden">
                    <div class="modal-body p-0">
                        <div class="row g-0">
                            <!-- ເບື້ອງຊ້າຍ -->
                            <div class="col-lg-5 bg-light border-end">
                                <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="w-100" style="object-fit: cover; height: 350px;">
                                <div class="p-4 overflow-auto" style="max-height: 500px;">
                                    <h4 class="fw-bold text-dark mb-4"><?php echo $row['tour_name']; ?></h4>
                                    
                                    <div class="detail-box">
                                        <div class="section-header"><i class="fas fa-info-circle"></i>ຂໍ້ມູນເບື້ອງຕົ້ນ</div>
                                        <div class="small mb-1"><b>ໝວດໝູ່:</b> <?php echo $row['category']; ?></div>
                                        <div class="small mb-1"><b>ໄລຍະເວລາ:</b> <?php echo $row['duration']; ?></div>
                                        <div class="small"><b>ຈຸດນັດພົບ:</b> <?php echo $row['meeting_point']; ?></div>
                                    </div>

                                    <!-- 2. ສິ່ງທີ່ລວມ ແລະ ບໍ່ລວມ (Included/Excluded) -->
                                    <div class="detail-box border-start border-4 border-success shadow-sm">
                                        <div class="section-header text-success"><i class="fas fa-check-circle"></i>ສິ່ງທີ່ລວມຢູ່ນຳ</div>
                                        <div class="small text-muted" style="white-space: pre-line;"><?php echo htmlspecialchars($row['whats_included']); ?></div>
                                    </div>
                                    <div class="detail-box border-start border-4 border-danger shadow-sm">
                                        <div class="section-header text-danger"><i class="fas fa-times-circle"></i>ສິ່ງທີ່ບໍ່ລວມ</div>
                                        <div class="small text-muted" style="white-space: pre-line;"><?php echo htmlspecialchars($row['whats_excluded']); ?></div>
                                    </div>

                                    <!-- ຄຳຄິດເຫັນ (Reviews) -->
                                    <h6 class="fw-bold text-dark mb-3 mt-4"><i class="fas fa-comment-dots me-2 text-primary"></i>ຄຳຄິດເຫັນຂອງລູກຄ້າ</h6>
                                    <div class="reviews-list">
                                        <?php 
                                        $rev_sql = "SELECT r.*, c.fullname FROM reviews r JOIN customers c ON r.customer_id = c.customer_id WHERE r.tour_id = $tid AND r.status = 'Approved' ORDER BY r.review_id DESC";
                                        $rev_res = mysqli_query($conn, $rev_sql);
                                        if(mysqli_num_rows($rev_res) > 0):
                                            while($rv = mysqli_fetch_assoc($rev_res)):
                                        ?>
                                            <div class="bg-white p-3 rounded-4 mb-2 shadow-sm small border-0">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <b><?php echo $rv['fullname']; ?></b>
                                                    <span class="text-warning"><?php for($i=1;$i<=5;$i++) echo ($i<=$rv['rating'])?'★':'☆'; ?></span>
                                                </div>
                                                <p class="mb-0 text-muted italic">"<?php echo $rv['comment']; ?>"</p>
                                            </div>
                                        <?php endwhile; else: echo "<p class='text-muted small italic text-center py-3'>ຍັງບໍ່ມີຄຳຄິດເຫັນ</p>"; endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ເບື້ອງຂວາ -->
                            <div class="col-lg-7 bg-white position-relative">
                                <div class="modal-body-content p-4 p-lg-5">
                                    <button type="button" class="btn-close float-end mb-4" data-bs-dismiss="modal"></button>
                                    <div class="section-header"><i class="fas fa-map-marked-alt"></i>ແຜນທີ່ ແລະ ເສັ້ນທາງເດີນທາງ</div>
                                    <div id="map-<?php echo $tid; ?>" class="itinerary-map shadow-sm"></div>

                                    <div class="section-header"><i class="fas fa-route"></i>ແຜນການເດີນທາງລະອຽດ</div>
                                    <div class="mb-5">
                                        <?php 
                                        $itinerary = json_decode($row['itinerary'], true);
                                        if($itinerary):
                                            foreach($itinerary as $day):
                                        ?>
                                            <div class="mb-4">
                                                <div class="badge bg-primary rounded-pill mb-3 px-3 py-2">ມື້ທີ <?php echo $day['day']; ?></div>
                                                <?php foreach($day['events'] as $ev): ?>
                                                    <div class="timeline-item">
                                                        <div class="fw-bold text-dark small"><?php echo htmlspecialchars($ev['location']); ?></div>
                                                        <div class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($ev['desc']); ?></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; endif; ?>
                                    </div>
                                </div>

                                <div class="sticky-booking-footer d-flex justify-content-between align-items-center">
                                    <div class="fw-bold text-danger fs-4"><?php echo number_format($row['price']); ?> <small class="fs-6 text-muted">ກີບ/ທ່ານ</small></div>
                                    <a href="booking_form.php?tour_id=<?php echo $tid; ?>" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-lg">ຈອງຕອນນີ້</a>
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
                const map = L.map(mapId).setView([17.9757, 102.6331], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                let allPoints = [];
                const colors = ['#0d6efd', '#198754', '#ff4757', '#fd7e14'];
                itiData.forEach((day, index) => {
                    let dayPoints = [];
                    day.events.forEach(ev => {
                        if (ev.lat && ev.lng) {
                            const pos = [parseFloat(ev.lat), parseFloat(ev.lng)];
                            dayPoints.push(pos); allPoints.push(pos);
                            L.marker(pos).addTo(map).bindPopup(`ມື້ ${day.day}: ${ev.location}`);
                        }
                    });
                    if (dayPoints.length > 1) {
                        L.polyline(dayPoints, {color: colors[index % colors.length], weight: 5, opacity: 0.7, dashArray: '10, 10'}).addTo(map);
                    }
                });
                if (allPoints.length > 0) map.fitBounds(L.polyline(allPoints).getBounds(), { padding: [50, 50] });
                map.invalidateSize(); 
            }, { once: true });
        })();
        </script>
        <?php endwhile; ?>
    </div>
</div>

<footer class="bg-dark text-white py-4 text-center mt-5">
    <p class="small opacity-50 mb-0">© 2026 Tour Booking System. All Rights Reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>