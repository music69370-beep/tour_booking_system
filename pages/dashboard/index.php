<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// 1. ດຶງຂໍ້ມູນສະຫຼຸບຈຳນວນທັງໝົດ (ສຳລັບ 5 ບ໋ອກເທິງສຸດ)
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings"))['total'] ?? 0;
$total_tours = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tours"))['total'] ?? 0;
$total_guides = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM guides"))['total'] ?? 0;
$total_coupons = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM coupons"))['total'] ?? 0;
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM customers"))['total'] ?? 0;

// 2. ກວດສອບຊ່ວງເວລາ (Date Filter)
$range = isset($_GET['range']) ? $_GET['range'] : 'month';
$start_date = "";
$prev_start = "";

switch ($range) {
    case 'today':
        $start_date = date('Y-m-d');
        $prev_start = date('Y-m-d', strtotime("-1 day"));
        break;
    case 'week':
        $start_date = date('Y-m-d', strtotime("-7 days"));
        $prev_start = date('Y-m-d', strtotime("-14 days"));
        break;
    case 'month':
    default:
        $start_date = date('Y-m-01');
        $prev_start = date('Y-m-01', strtotime("-1 month"));
        break;
}

// 3. ດຶງຂໍ້ມູນລາຍຮັບ
function getRevenue($conn, $start, $end = null) {
    $sql = "SELECT SUM(amount) as total FROM payments WHERE DATE(payment_date) >= '$start'";
    if($end) $sql .= " AND DATE(payment_date) < '$end'";
    $res = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    return $res['total'] ?? 0;
}

$current_rev = getRevenue($conn, $start_date);
$prev_rev = getRevenue($conn, $prev_start, $start_date);
$rev_growth = ($prev_rev > 0) ? (($current_rev - $prev_rev) / $prev_rev) * 100 : 0;

// 4. ເປົ້າໝາຍຍອດຂາຍ (ສົມມຸດ 100 ລ້ານ)
$sales_target = 100000000;
$target_percent = ($current_rev / $sales_target) * 100;

// 5. ຂໍ້ມູນກຣາຟ (ຄືເກົ່າ)
$status_labels = []; $status_data = [];
$st_query = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
while($st = mysqli_fetch_assoc($st_query)) {
    $status_labels[] = ($st['status'] == 'Confirmed') ? 'ຢືນຢັນແລ້ວ' : (($st['status'] == 'Cancelled') ? 'ຍົກເລີກ' : 'ລໍຖ້າອະນຸມັດ');
    $status_data[] = $st['count'];
}

$top_tour_names = []; $top_tour_counts = [];
$top_query = mysqli_query($conn, "SELECT t.tour_name, COUNT(b.booking_id) as total FROM tours t JOIN bookings b ON t.tour_id = b.tour_id GROUP BY t.tour_id ORDER BY total DESC LIMIT 5");
while($top = mysqli_fetch_assoc($top_query)) {
    $top_tour_names[] = $top['tour_name'];
    $top_tour_counts[] = $top['total'];
}

$line_labels = []; $line_data = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $line_labels[] = date('d/m', strtotime($d));
    $rev_day = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM payments WHERE DATE(payment_date) = '$d'"));
    $line_data[] = (float)($rev_day['total'] ?? 0);
}
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <div>
                <h2 class="fw-bold text-dark mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Dashboard ພາບລວມທຸລະກິດ</h2>
                <p class="text-muted small mb-0">ລາຍງານປະສິດທິພາບການດຳເນີນງານ</p>
            </div>
            <div class="btn-group shadow-sm p-1 bg-white rounded-pill">
                <a href="?range=today" class="btn rounded-pill px-3 <?php echo ($range == 'today') ? 'btn-primary' : 'btn-light'; ?> small">ມື້ນີ້</a>
                <a href="?range=week" class="btn rounded-pill px-3 <?php echo ($range == 'week') ? 'btn-primary' : 'btn-light'; ?> small">7 ວັນ</a>
                <a href="?range=month" class="btn rounded-pill px-3 <?php echo ($range == 'month') ? 'btn-primary' : 'btn-light'; ?> small">ເດືອນນີ້</a>
            </div>
        </div>

        <!-- *** ສ່ວນທີ່ເພີ່ມໃໝ່: 5 ບ໋ອກສະຫຼຸບທັງໝົດ *** -->
        <div class="row g-3 mb-4">
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-white h-100">
                    <div class="text-primary mb-1"><i class="fas fa-calendar-check fa-lg"></i></div>
                    <small class="text-muted d-block small">ການຈອງທັງໝົດ</small>
                    <h4 class="fw-bold mb-0"><?php echo number_format($total_bookings); ?></h4>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-white h-100">
                    <div class="text-info mb-1"><i class="fas fa-map-marked-alt fa-lg"></i></div>
                    <small class="text-muted d-block small">ແພັກເກັດທົວ</small>
                    <h4 class="fw-bold mb-0"><?php echo number_format($total_tours); ?></h4>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-white h-100">
                    <div class="text-success mb-1"><i class="fas fa-user-tie fa-lg"></i></div>
                    <small class="text-muted d-block small">ໄກ້ນຳທ່ຽວ</small>
                    <h4 class="fw-bold mb-0"><?php echo number_format($total_guides); ?></h4>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-white h-100">
                    <div class="text-danger mb-1"><i class="fas fa-ticket-alt fa-lg"></i></div>
                    <small class="text-muted d-block small">ຄູປອງທັງໝົດ</small>
                    <h4 class="fw-bold mb-0"><?php echo number_format($total_coupons); ?></h4>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-white h-100">
                    <div class="text-warning mb-1"><i class="fas fa-users fa-lg"></i></div>
                    <small class="text-muted d-block small">ລູກຄ້າທັງໝົດ</small>
                    <h4 class="fw-bold mb-0"><?php echo number_format($total_customers); ?></h4>
                </div>
            </div>
        </div>

        <!-- Row 1: Revenue & Target (ຄືເກົ່າ) -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between mb-3">
                        <h6 class="text-muted fw-bold">ລາຍຮັບສຸດທິ (Net Revenue)</h6>
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary"><i class="fas fa-wallet"></i></div>
                    </div>
                    <h2 class="fw-bold mb-1">₭ <?php echo number_format($current_rev); ?></h2>
                    <div class="small">
                        <?php if($rev_growth >= 0): ?>
                            <span class="text-success fw-bold"><i class="fas fa-arrow-up me-1"></i> <?php echo round($rev_growth, 1); ?>%</span>
                        <?php else: ?>
                            <span class="text-danger fw-bold"><i class="fas fa-arrow-down me-1"></i> <?php echo round(abs($rev_growth), 1); ?>%</span>
                        <?php endif; ?>
                        <span class="text-muted ms-1">ທຽບກັບຊ່ວງກ່ອນ</span>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0">ເປົ້າໝາຍຍອດຂາຍປະຈຳເດືອນ (Sales Target)</h6>
                        <span class="badge bg-light text-dark border fw-normal">ເປົ້າໝາຍ: 100,000,000 ກີບ</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="progress flex-grow-1" style="height: 15px; border-radius: 50px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: <?php echo min($target_percent, 100); ?>%"></div>
                        </div>
                        <h4 class="fw-bold mb-0 text-success"><?php echo round($target_percent, 1); ?>%</h4>
                    </div>
                    <p class="small text-muted mt-2 mb-0">ອີກ <?php echo number_format(max($sales_target - $current_rev, 0)); ?> ກີບ ຈະຮອດເປົ້າໝາຍ</p>
                </div>
            </div>
        </div>

        <!-- Row 2: Charts (ຄືເກົ່າ) -->
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <h5 class="fw-bold mb-4">ແນວໂນ້ມການຈ່າຍເງິນ (7 ວັນຫຼ້າສຸດ)</h5>
                    <div style="height: 300px;"><canvas id="revenueLineChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white text-center">
                    <h5 class="fw-bold mb-4">ສັດສ່ວນສະຖານະການຈອງ</h5>
                    <div style="height: 300px;"><canvas id="statusPieChart"></canvas></div>
                </div>
            </div>
        </div>

        <!-- Row 3: Bottom Charts & Table (ຄືເກົ່າ) -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <h5 class="fw-bold mb-4">5 ອັນດັບແພັກເກັດທົວທີ່ຂາຍດີທີ່ສຸດ</h5>
                    <div style="height: 300px;"><canvas id="topTourChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                    <div class="card-header bg-white py-3 border-0"><h5 class="fw-bold mb-0">ການຈອງຫຼ້າສຸດ</h5></div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small text-uppercase">
                                <tr><th class="ps-4">ລູກຄ້າ</th><th>ແພັກເກັດ</th><th class="text-end">ລາຄາລວມ</th><th class="text-center">ສະຖານະ</th></tr>
                            </thead>
                            <tbody>
                                <?php 
                                $recent = mysqli_query($conn, "SELECT b.*, c.fullname, t.tour_name FROM bookings b JOIN customers c ON b.customer_id=c.customer_id JOIN tours t ON b.tour_id=t.tour_id ORDER BY b.booking_id DESC LIMIT 5");
                                while($r = mysqli_fetch_assoc($recent)): 
                                    $st = $r['status'];
                                    $badge = ($st=='Confirmed') ? 'bg-success' : (($st=='Cancelled') ? 'bg-danger' : 'bg-warning text-dark');
                                ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold small"><?php echo $r['fullname']; ?></div>
                                            <small class="text-muted" style="font-size:0.65rem"><?php echo date('d/m H:i', strtotime($r['booking_date'])); ?></small>
                                        </td>
                                        <td class="small text-truncate" style="max-width: 150px;"><?php echo $r['tour_name']; ?></td>
                                        <td class="text-end fw-bold text-danger small">₭ <?php echo number_format($r['total_price']); ?></td>
                                        <td class="text-center"><span class="badge rounded-pill <?php echo $badge; ?>" style="font-size: 0.6rem;"><?php echo ($st=='Confirmed')?'ສຳເລັດ':'ລໍຖ້າ'; ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart
    new Chart(document.getElementById('revenueLineChart').getContext('2d'), {
        type: 'line',
        data: { labels: <?php echo json_encode($line_labels); ?>, datasets: [{ label: 'ລາຍຮັບ', data: <?php echo json_encode($line_data); ?>, borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', fill: true, tension: 0.4, borderWidth: 3 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    // Pie Chart
    new Chart(document.getElementById('statusPieChart').getContext('2d'), {
        type: 'doughnut',
        data: { labels: <?php echo json_encode($status_labels); ?>, datasets: [{ data: <?php echo json_encode($status_data); ?>, backgroundColor: ['#198754', '#dc3545', '#ffc107'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom' } } }
    });

    // Bar Chart
    new Chart(document.getElementById('topTourChart').getContext('2d'), {
        type: 'bar',
        data: { labels: <?php echo json_encode($top_tour_names); ?>, datasets: [{ label: 'ການຈອງ', data: <?php echo json_encode($top_tour_counts); ?>, backgroundColor: '#4e73df', borderRadius: 10 }] },
        options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } } }
    });
</script>

<?php include '../../includes/footer.php'; ?>