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

<!-- ສ່ວນເນື້ອຫາຫຼັກ -->
<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    
    <!-- ເອີ້ນໃຊ້ Navbar ທີ່ເຮົາສ້າງໃໝ່ (ຂໍ້ມູນຜູ້ໃຊ້ຈະຢູ່ທີ່ນີ້) -->
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-th-large text-primary me-2"></i>ແຜງຄວບຄຸມ (Dashboard)</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>

        <!-- 1. Stats Cards Section -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary text-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-map-marked-alt fa-lg"></i></div>
                        <div>
                            <p class="mb-0 small opacity-75">ແພັກເກັດທົວ</p>
                            <h3 class="fw-bold mb-0"><?php echo $tours_count; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-success text-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-users fa-lg"></i></div>
                        <div>
                            <p class="mb-0 small opacity-75">ລູກຄ້າທັງໝົດ</p>
                            <h3 class="fw-bold mb-0"><?php echo $customers_count; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-warning text-dark h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-clock fa-lg"></i></div>
                        <div>
                            <p class="mb-0 small opacity-75">ລໍຖ້າຢືນຢັນ</p>
                            <h3 class="fw-bold mb-0"><?php echo $pending_count; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-danger text-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-wallet fa-lg"></i></div>
                        <div>
                            <p class="mb-0 small opacity-75">ລາຍຮັບລວມ (ກີບ)</p>
                            <h4 class="fw-bold mb-0"><?php echo number_format($total_revenue); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Charts Section -->
        <div class="row g-4 mb-4">
            <!-- ກຣາຟເສັ້ນ (ລາຍຮັບ) -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4">ແນວໂນ້ມລາຍຮັບ (7 ວັນຫຼ້າສຸດ)</h5>
                    <div style="height: 350px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- ກຣາຟວົງມົນ (ທົວຂາຍດີ) -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4 text-center">5 ອັນດັບທົວຂາຍດີ</h5>
                    <div style="height: 350px;">
                        <canvas id="tourPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Recent Bookings Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark">ລາຍການຈອງຫຼ້າສຸດ</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">ວັນທີຈອງ</th>
                                <th>ຊື່ລູກຄ້າ</th>
                                <th>ທົວ</th>
                                <th class="text-end">ລາຄາລວມ</th>
                                <th class="text-center">ສະຖານະ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res = mysqli_query($conn, "
                                SELECT b.*, c.fullname, t.tour_name 
                                FROM bookings b 
                                JOIN customers c ON b.customer_id=c.customer_id 
                                JOIN tours t ON b.tour_id=t.tour_id 
                                ORDER BY b.booking_id DESC LIMIT 5
                            ");
                            while($row = mysqli_fetch_assoc($res)):
                            ?>
                            <tr>
                                <td class="ps-4 small text-muted"><?php echo date('d/m/Y H:i', strtotime($row['booking_date'])); ?></td>
                                <td class="fw-bold"><?php echo $row['fullname']; ?></td>
                                <td><?php echo $row['tour_name']; ?></td>
                                <td class="text-end fw-bold text-danger"><?php echo number_format($row['total_price']); ?></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?php echo ($row['status']=='Confirmed') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Line Chart: ລາຍຮັບ
    const ctxLine = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'ລາຍຮັບ (ກີບ)',
                data: <?php echo json_encode($chart_data); ?>,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { left: 0, right: 10, top: 0, bottom: 0 } },
            plugins: { legend: { display: false } },
            scales: { 
                y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
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
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#858796'],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: 10 },
            cutout: '70%',
            plugins: { 
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } } 
            }
        }
    });
</script>

<?php include '../../includes/footer.php'; ?>