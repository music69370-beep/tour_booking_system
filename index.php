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
            <div class="card tour-card shadow-sm h-100">
                <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="card-img-top tour-img">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-dark mb-1"><?php echo $row['tour_name']; ?></h5>
                    <p class="text-muted small mb-2"><i class="far fa-clock me-1"></i> ໄລຍະເວລາ: <?php echo $row['duration']; ?></p>
                    
                    <!-- ສະແດງບ່ອນນັ່ງ -->
                    <div class="mb-3">
                        <small class="text-muted"><i class="fas fa-chair me-1"></i> ບ່ອນນັ່ງຫວ່າງ:</small>
                        <span class="badge <?php echo ($remaining <= 2) ? 'bg-danger' : 'bg-success'; ?> ms-1">
                            <?php echo $remaining; ?> / <?php echo $row['max_seats']; ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <small class="text-muted d-block">ລາຄາເລີ່ມຕົ້ນ</small>
                            <span class="price-tag"><?php echo number_format($row['price']); ?> <small style="font-size: 0.8rem;">ກີບ</small></span>
                        </div>
                        
                        <?php if($remaining > 0): ?>
                            <a href="booking_form.php?tour_id=<?php echo $row['tour_id']; ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">ຈອງເລີຍ</a>
                        <?php else: ?>
                            <button class="btn btn-secondary rounded-pill px-4" disabled>ເຕັມແລ້ວ</button>
                        <?php endif; ?>
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