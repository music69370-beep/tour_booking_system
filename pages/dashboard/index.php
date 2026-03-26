<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// 1. ດຶງຂໍ້ມູນສະຖິຕິພື້ນຖານ
$tours_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tours"))['total'];
$customers_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM customers"))['total'];
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status='Pending'"))['total'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM payments"))['total'] ?? 0;

// 2. ດຶງຂໍ້ມູນສຳລັບກຣາຟລາຍຮັບ (7 ວັນຫຼ້າສຸດ)
$chart_labels = [];
$chart_data = [];
$revenue_query = mysqli_query($conn, "
    SELECT DATE(payment_date) as d, SUM(amount) as total 
    FROM payments 
    WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(payment_date) 
    ORDER BY d ASC
");
while($row = mysqli_fetch_assoc($revenue_query)) {
    $chart_labels[] = date('d/m', strtotime($row['d']));
    $chart_data[] = $row['total'];
}

// 3. ດຶງຂໍ້ມູນທົວທີ່ໄດ້ຮັບຄວາມນິຍົມ (Top 5 Tours)
$tour_names = [];
$tour_bookings = [];
$pop_query = mysqli_query($conn, "
    SELECT t.tour_name, COUNT(b.booking_id) as count 
    FROM tours t 
    LEFT JOIN bookings b ON t.tour_id = b.tour_id 
    GROUP BY t.tour_id 
    ORDER BY count DESC LIMIT 5
");
while($row = mysqli_fetch_assoc($pop_query)) {
    $tour_names[] = $row['tour_name'];
    $tour_bookings[] = $row['count'];
}
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h2 class="fw-bold"><i class="fas fa-chart-line text-primary me-2"></i>ພາບລວມລະບົບ</h2>
        <span class="badge bg-white text-dark border p-2 rounded-pill shadow-sm">
            <i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y'); ?>
        </span>
    </div>

    <!-- 1. Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-map-marked-alt fa-lg"></i></div>
                    <div>
                        <p class="mb-0 small">ແພັກເກັດທົວ</p>
                        <h3 class="fw-bold mb-0"><?php echo $tours_count; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-success text-white">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-users fa-lg"></i></div>
                    <div>
                        <p class="mb-0 small">ລູກຄ້າທັງໝົດ</p>
                        <h3 class="fw-bold mb-0"><?php echo $customers_count; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-warning text-dark">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-clock fa-lg"></i></div>
                    <div>
                        <p class="mb-0 small">ລໍຖ້າຢືນຢັນ</p>
                        <h3 class="fw-bold mb-0"><?php echo $pending_count; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-danger text-white">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-wallet fa-lg"></i></div>
                    <div>
                        <p class="mb-0 small">ລາຍຮັບລວມ (ກີບ)</p>
                        <h4 class="fw-bold mb-0"><?php echo number_format($total_revenue); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Charts Section -->
    <!-- 2. Charts Section -->
    <div class="row g-4 mb-5">
        <!-- ກຣາຟເສັ້ນ (ລາຍຮັບ) -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3"> <!-- ຫຼຸດ padding ເຫຼືອ p-3 -->
                    <h5 class="fw-bold mb-3 ms-2">ແນວໂນ້ມລາຍຮັບ (7 ວັນຫຼ້າສຸດ)</h5>
                    <div style="height: 350px; width: 100%;"> <!-- ປັບສູງເປັນ 350px -->
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ກຣາຟວົງມົນ (ທົວຂາຍດີ) -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3"> <!-- ຫຼຸດ padding ເຫຼືອ p-3 -->
                    <h5 class="fw-bold mb-3 ms-2 text-center">5 ອັນດັບທົວຂາຍດີ</h5>
                    <div style="height: 350px; width: 100%;"> <!-- ປັບສູງເປັນ 350px -->
                        <canvas id="tourPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- ເອີ້ນໃຊ້ Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Line Chart: ລາຍຮັບ
    // 1. Line Chart: ລາຍຮັບ
    const ctxLine = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'ລາຍຮັບ (ກີບ)',
                data: <?php echo json_encode($chart_data); ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { left: 0, right: 10, top: 0, bottom: 0 } }, // ຕັ້ງ padding ເປັນ 0
            plugins: { legend: { display: false } },
            scales: { 
                y: { beginAtZero: true, grid: { display: true } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Doughnut Chart: ທົວຂາຍດີ
    const ctxPie = document.getElementById('tourPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($tour_names); ?>,
            datasets: [{
                data: <?php echo json_encode($tour_bookings); ?>,
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d'],
                borderWidth: 0,
                hoverOffset: 15 // ເພີ່ມ effect ເວລາເອົາເມົາສ໌ໄປຊີ້
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: 10 }, // ເພີ່ມໄລຍະຫ່າງໜ້ອຍໜຶ່ງໃຫ້ວົງມົນບໍ່ຕິດຂອບເກີນໄປ
            cutout: '65%', // ປັບຂະໜາດຮູທາງໃນ
            plugins: { 
                legend: { 
                    position: 'bottom', 
                    labels: { 
                        usePointStyle: true, 
                        padding: 15, // ຫຼຸດໄລຍະຫ່າງຂອງ Legend
                        font: { size: 12 }
                    } 
                } 
            }
        }
    });
</script>

<?php include '../../includes/footer.php'; ?>