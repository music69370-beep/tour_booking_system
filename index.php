<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Booking - ຈອງທົວທ່ຽວລາວ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fa; }
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=1350&q=80');
            background-size: cover; background-position: center; color: white; padding: 80px 0; text-align: center;
        }
        .tour-card { border: none; border-radius: 20px; transition: transform 0.3s; overflow: hidden; }
        .tour-card:hover { transform: translateY(-10px); }
        .tour-img { height: 220px; object-fit: cover; }
        .price-tag { color: #ff4757; font-size: 1.3rem; font-weight: bold; }
    </style>
</head>
<body>

<!-- Navbar ສໍາລັບລູກຄ້າ -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-plane-departure me-2"></i>TourBooking</a>
        <div class="ms-auto">
            <a href="login.php" class="btn btn-outline-light btn-sm rounded-pill px-3">ສຳລັບເຈົ້າໜ້າທີ່</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-section">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">ສະບາຍດີ! ໄປທ່ຽວໃສດີມື້ນີ້?</h1>
        <p class="lead">ຄົ້ນພົບແພັກເກັດທົວທີ່ດີທີ່ສຸດ ພ້ອມລາຄາສຸດພິເສດສຳລັບທ່ານ</p>
    </div>
</header>

<div class="container my-5">
    <h3 class="fw-bold mb-4"><i class="fas fa-star text-warning me-2"></i>ແພັກເກັດທີ່ກຳລັງມາແຮງ</h3>
    <div class="row g-4">
        <?php
        $sql = "SELECT * FROM tours WHERE status = 'Active' ORDER BY tour_id DESC";
        $result = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($result) > 0):
            while($row = mysqli_fetch_assoc($result)):
                
                // --- Logic ຄຳນວນບ່ອນນັ່ງຫວ່າງ ---
                $tid = $row['tour_id'];
                $booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
                $booked_count = $booked_res['total'] ?? 0;
                $remaining = $row['max_seats'] - $booked_count;
        ?>
        <div class="col-md-4">
            <!-- ບ່ອນ Loop ສະແດງ Card ທົວ -->
            <div class="card tour-card shadow-sm h-100">
                <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="card-img-top tour-img">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-dark"><?php echo $row['tour_name']; ?></h5>
                    <div class="mb-2">
                        <span class="badge bg-light text-primary border"><i class="fas fa-utensils me-1"></i> ອາຫານ <?php echo $row['meals']; ?> ຄາບ</span>
                        <span class="badge bg-light text-success border"><i class="fas fa-clock me-1"></i> <?php echo $row['duration']; ?></span>
                    </div>
                    
                    <!-- ໂຊກິດຈະກຳຫຍໍ້ໆ -->
                    <p class="small text-muted mb-3 text-truncate"><?php echo $row['activities']; ?></p>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="price-tag text-danger fw-bold"><?php echo number_format($row['price']); ?> ກີບ</span>
                        
                        <!-- ປຸ່ມເບິ່ງແຜນການເດີນທາງ -->
                        <button class="btn btn-outline-info btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modal<?php echo $row['tour_id']; ?>">ລາຍລະອຽດ</button>
                        
                        <a href="booking_form.php?tour_id=<?php echo $row['tour_id']; ?>" class="btn btn-primary btn-sm rounded-pill shadow-sm">ຈອງເລີຍ</a>
                    </div>
                </div>
            </div>

            <!-- Modal ສະແດງແຜນການເດີນທາງ -->
            <div class="modal fade" id="modal<?php echo $row['tour_id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content rounded-4 border-0">
                        <div class="modal-header bg-primary text-white border-0">
                            <h5 class="modal-title fw-bold">ແຜນການເດີນທາງ: <?php echo $row['tour_name']; ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <h6 class="fw-bold text-primary"><i class="fas fa-map-marker-alt me-2"></i>ຕາຕະລາງການທ່ອງທ່ຽວ:</h6>
                            <div class="bg-light p-3 rounded-3 mb-4" style="white-space: pre-line;">
                                <?php echo $row['itinerary']; ?>
                            </div>
                            <h6 class="fw-bold text-success"><i class="fas fa-star me-2"></i>ກິດຈະກຳ ແລະ ສິ່ງທີ່ທ່ານຈະໄດ້ຮັບ:</h6>
                            <p><?php echo $row['activities']; ?></p>
                            <hr>
                            <p class="mb-0 text-muted">ອາຫານທັງໝົດ: <strong><?php echo $row['meals']; ?></strong> ຄາບ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            endwhile; 
        else:
            echo "<div class='col-12 text-center py-5'><p class='text-muted'>ຂໍອະໄພ, ຍັງບໍ່ມີທົວວ່າງໃນຕອນນີ້.</p></div>";
        endif;
        ?>
    </div>
</div>

<footer class="bg-white border-top py-4">
    <div class="container text-center text-muted small">
        <p>© 2026 Tour Booking System. ລະບົບຈອງທົວທີ່ດີທີ່ສຸດໃນລາວ.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>