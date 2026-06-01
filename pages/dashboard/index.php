<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// 1. ດຶງຂໍ້ມູນສະຫຼຸບຈຳນວນ
$c_book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings"))['c'];
$c_tour = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tours"))['c'];
$c_guide = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM guides"))['c'];
$c_cust = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM customers"))['c'];

// 2. ກວດສອບຊ່ວງເວລາ (Date Filter)
$range = isset($_GET['range']) ? $_GET['range'] : 'month';
$start_date = ($range == 'today') ? date('Y-m-d') : (($range == 'week') ? date('Y-m-d', strtotime("-7 days")) : date('Y-m-01'));

// 3. ດຶງຂໍ້ມູນການເງິນ ( Admin Only )
$total_revenue = 0; 
$sales_target = 100000000; // ເປົ້າໝາຍ 100 ລ້ານ
$target_percent = 0;

if (isAdmin()) {
    $rev_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as confirmed_rev FROM bookings WHERE status = 'Confirmed' AND DATE(booking_date) >= '$start_date'"));
    $total_revenue = $rev_res['confirmed_rev'] ?? 0;
    $target_percent = ($total_revenue / $sales_target) * 100;
}

// 4. ຂໍ້ມູນກຣາຟ ແລະ ລາຍການເພີ່ມເຕີມ
$line_labels = []; $line_data = []; $status_labels = []; $status_data = [];
if (isAdmin()) {
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $line_labels[] = date('d/m', strtotime($d));
        $val = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as t FROM bookings WHERE status = 'Confirmed' AND DATE(booking_date) = '$d'"))['t'];
        $line_data[] = (float)($val ?? 0);
    }
    $st_query = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
    while($st = mysqli_fetch_assoc($st_query)) {
        $status_labels[] = ($st['status'] == 'Confirmed') ? 'ອະນຸມັດ' : (($st['status'] == 'Cancelled') ? 'ຍົກເລີກ' : 'ລໍຖ້າ');
        $status_data[] = $st['count'];
    }
}
?>

<style>
    /* ຕົບແຕ່ງພິເສດ */
    .main-content { background-color: #f4f7f6; }
    .stat-card {
        border: none;
        border-radius: 20px;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .icon-shape {
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }
    .progress {
        background-color: #e9ecef;
        border-radius: 50px;
        overflow: hidden;
    }
    .card-title-custom { font-size: 1.1rem; font-weight: 700; color: #333; margin-bottom: 20px; }
    .table thead th { background-color: #f8f9fa; border: none; font-size: 0.8rem; text-transform: uppercase; color: #888; }
    .table tbody td { border-bottom: 1px solid #f0f0f0; padding: 15px 10px; }
    .badge-soft-success { background-color: #e1f5ea; color: #198754; }
    .badge-soft-warning { background-color: #fff9db; color: #f08c00; }
    .badge-soft-danger { background-color: #ffe3e3; color: #e03131; }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">ສະບາຍດີ, <?php echo explode(' ', $_SESSION['fullname'])[0]; ?>! 👋</h2>
                <p class="text-muted mb-0">ນີ້ແມ່ນພາບລວມການເຮັດວຽກຂອງລະບົບໃນມື້ນີ້.</p>
            </div>
            <div class="btn-group shadow-sm p-1 bg-white rounded-pill">
                <a href="?range=today" class="btn rounded-pill px-4 <?php echo ($range == 'today') ? 'btn-primary' : 'btn-light'; ?> border-0">ມື້ນີ້</a>
                <a href="?range=week" class="btn rounded-pill px-4 <?php echo ($range == 'week') ? 'btn-primary' : 'btn-light'; ?> border-0">7 ວັນ</a>
                <a href="?range=month" class="btn rounded-pill px-4 <?php echo ($range == 'month') ? 'btn-primary' : 'btn-light'; ?> border-0">ເດືອນນີ້</a>
            </div>
        </div>

        <!-- Section 1: Top Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card shadow-sm bg-primary text-white p-4">
                    <div class="icon-shape"><i class="fas fa-calendar-check"></i></div>
                    <h5 class="opacity-75 mb-1 small">ລາຍການຈອງທັງໝົດ</h5>
                    <h2 class="fw-bold mb-0"><?php echo number_format($c_book); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm bg-success text-white p-4">
                    <div class="icon-shape"><i class="fas fa-map-marked-alt"></i></div>
                    <h5 class="opacity-75 mb-1 small">ແພັກເກັດທີ່ເປີດຂາຍ</h5>
                    <h2 class="fw-bold mb-0"><?php echo number_format($c_tour); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm bg-info text-white p-4">
                    <div class="icon-shape"><i class="fas fa-user-tie"></i></div>
                    <h5 class="opacity-75 mb-1 small">ໄກ້ຜູ້ນຳທ່ຽວ</h5>
                    <h2 class="fw-bold mb-0"><?php echo number_format($c_guide); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm bg-warning text-white p-4">
                    <div class="icon-shape"><i class="fas fa-users"></i></div>
                    <h5 class="opacity-75 mb-1 small">ລູກຄ້າທັງໝົດ</h5>
                    <h2 class="fw-bold mb-0"><?php echo number_format($c_cust); ?></h2>
                </div>
            </div>
        </div>

        <?php if(isAdmin()): ?>
        <!-- Section 2: Financial Focus -->
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <h6 class="text-muted fw-bold small text-uppercase mb-3">ລາຍຮັບສຸດທິ (Confirmed)</h6>
                    <h1 class="fw-bold text-primary display-5 mb-2">₭ <?php echo number_format($total_revenue); ?></h1>
                    <div class="mt-auto pt-3 border-top">
                        <span class="text-success fw-bold"><i class="fas fa-arrow-up"></i> ລາຍຮັບທີ່ຢືນຢັນແລ້ວ</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="text-muted fw-bold small text-uppercase mb-0">ເປົ້າໝາຍຍອດຂາຍປະຈຳເດືອນ (100 ລ້ານ)</h6>
                        <span class="fw-bold text-dark"><?php echo round($target_percent, 1); ?>%</span>
                    </div>
                    <div class="progress mb-4" style="height: 25px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                             style="width: <?php echo min($target_percent, 100); ?>%">
                             <?php echo (round($target_percent) >= 10) ? round($target_percent).'%' : ''; ?>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-4 border-end">
                            <small class="text-muted d-block">ຂາດອີກ</small>
                            <span class="fw-bold text-danger">₭ <?php echo number_format(max($sales_target - $total_revenue, 0)); ?></span>
                        </div>
                        <div class="col-4 border-end">
                            <small class="text-muted d-block">ສະເລ່ຍ/ມື້</small>
                            <span class="fw-bold text-dark">₭ <?php echo number_format($total_revenue / date('d')); ?></span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">ສະຖານະ</small>
                            <span class="badge bg-soft-success rounded-pill px-3">ກຳລັງເຕີບໂຕ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Graphs & Analytics -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <h6 class="card-title-custom"><i class="fas fa-chart-area text-primary me-2"></i>ແນວໂນ້ມລາຍຮັບ 7 ວັນຫຼ້າສຸດ</h6>
                    <div style="height: 350px;"><canvas id="revenueLineChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white text-center">
                    <h6 class="card-title-custom"><i class="fas fa-chart-pie text-danger me-2"></i>ສະຖານະການຈອງ</h6>
                    <div style="height: 300px;"><canvas id="statusPieChart"></canvas></div>
                    <div class="mt-3 d-flex justify-content-center gap-3 small">
                        <span class="text-success">● ອະນຸມັດ</span>
                        <span class="text-danger">● ຍົກເລີກ</span>
                        <span class="text-warning">● ລໍຖ້າ</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Data Lists -->
        <div class="row g-4">
            <!-- Best Selling Tours -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0"><i class="fas fa-fire text-orange me-2"></i>ແພັກເກັດທົວທີ່ຂາຍດີທີ່ສຸດ</h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php 
                            $best_query = mysqli_query($conn, "SELECT t.tour_name, t.image, COUNT(b.booking_id) as total_sold FROM tours t JOIN bookings b ON t.tour_id = b.tour_id GROUP BY t.tour_id ORDER BY total_sold DESC LIMIT 5");
                            while($bt = mysqli_fetch_assoc($best_query)):
                            ?>
                            <li class="list-group-item d-flex align-items-center border-0 px-4 py-3">
                                <img src="../../assets/uploads/tours/<?php echo $bt['image']; ?>" class="rounded-3 me-3" width="50" height="50" style="object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold small"><?php echo $bt['tour_name']; ?></h6>
                                    <small class="text-muted">ຂາຍອອກແລ້ວ <?php echo $bt['total_sold']; ?> ລາຍການ</small>
                                </div>
                                <span class="badge bg-soft-success rounded-pill px-3">Top Sell</span>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings Table -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0"><i class="fas fa-clock text-info me-2"></i>ລາຍການຈອງຫຼ້າສຸດ</h6>
                        <a href="../bookings/index.php" class="btn btn-sm btn-light rounded-pill px-3 small">ເບິ່ງທັງໝົດ</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">ລູກຄ້າ</th>
                                    <th>ແພັກເກັດ</th>
                                    <th class="text-end">ຍອດລວມ</th>
                                    <th class="text-center">ສະຖານະ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $recent = mysqli_query($conn, "SELECT b.*, c.fullname, t.tour_name FROM bookings b JOIN customers c ON b.customer_id=c.customer_id JOIN tours t ON b.tour_id=t.tour_id ORDER BY b.booking_id DESC LIMIT 5");
                                while($r = mysqli_fetch_assoc($recent)): 
                                    $st = $r['status'];
                                    $st_class = ($st=='Confirmed') ? 'badge-soft-success' : (($st=='Cancelled') ? 'badge-soft-danger' : 'badge-soft-warning');
                                    $st_label = ($st=='Confirmed') ? 'ສຳເລັດ' : (($st=='Cancelled') ? 'ຍົກເລີກ' : 'ລໍຖ້າ');
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold small"><?php echo $r['fullname']; ?></div>
                                        <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('d/m/Y H:i', strtotime($r['booking_date'])); ?></small>
                                    </td>
                                    <td class="small text-truncate" style="max-width: 180px;"><?php echo $r['tour_name']; ?></td>
                                    <td class="text-end fw-bold text-danger">₭ <?php echo number_format($r['total_price']); ?></td>
                                    <td class="text-center"><span class="badge rounded-pill <?php echo $st_class; ?> px-3"><?php echo $st_label; ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php if(isAdmin()): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart: Revenue Trend
    const lineCtx = document.getElementById('revenueLineChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($line_labels); ?>,
            datasets: [{
                label: 'ລາຍຮັບປະຈຳມື້',
                data: <?php echo json_encode($line_data); ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 4,
                tension: 0.4,
                fill: true,
                pointRadius: 6,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#0d6efd',
                pointBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f0f0f0' }, ticks: { callback: v => v.toLocaleString() + ' ₭' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Pie Chart: Booking Status
    const pieCtx = document.getElementById('statusPieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($status_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($status_data); ?>,
                backgroundColor: ['#198754', '#e03131', '#f08c00'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: { legend: { display: false } }
        }
    });
</script>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>