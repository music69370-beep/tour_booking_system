<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// 1. ດຶງຂໍ້ມູນສະຫຼຸບຈຳນວນ ( Everyone sees these )
$c_book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings"))['c'];
$c_tour = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tours"))['c'];
$c_guide = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM guides"))['c'];
$c_coupon = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM coupons"))['c'];
$c_cust = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM customers"))['c'];

// 2. ກວດສອບຊ່ວງເວລາ (Date Filter)
$range = isset($_GET['range']) ? $_GET['range'] : 'month';
$start_date = ($range == 'today') ? date('Y-m-d') : (($range == 'week') ? date('Y-m-d', strtotime("-7 days")) : date('Y-m-01'));
$prev_start = ($range == 'month') ? date('Y-m-01', strtotime("-1 month")) : date('Y-m-d', strtotime("-14 days"));

// 3. ດຶງຂໍ້ມູນການເງິນ ( Admin Only )
$total_revenue = 0; $net_profit = 0; $target_percent = 0; $rev_growth = 0;
$sales_target = 100000000;

if (isAdmin()) {
    $rev_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT (SELECT SUM(amount) FROM payments WHERE DATE(payment_date) >= '$start_date') as paid, (SELECT SUM(refund_amount) FROM bookings WHERE status='Cancelled' AND travel_date >= '$start_date') as ref"));
    $total_revenue = ($rev_data['paid'] ?? 0) - ($rev_data['ref'] ?? 0);

    $prev_rev_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT (SELECT SUM(amount) FROM payments WHERE DATE(payment_date) >= '$prev_start' AND DATE(payment_date) < '$start_date') as paid"));
    $prev_rev = $prev_rev_data['paid'] ?? 0;
    $rev_growth = ($prev_rev > 0) ? (($total_revenue - $prev_rev) / $prev_rev) * 100 : 0;

    $cost_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT (SELECT SUM(b.num_people * t.cost_per_person) FROM bookings b JOIN tours t ON b.tour_id = t.tour_id WHERE b.status = 'Confirmed' AND b.travel_date >= '$start_date') as active_cost, (SELECT SUM(cancellation_cost) FROM bookings WHERE status = 'Cancelled' AND travel_date >= '$start_date') as lost_cost"));
    $total_cost = ($cost_res['active_cost'] ?? 0) + ($cost_res['lost_cost'] ?? 0);
    $net_profit = $total_revenue - $total_cost;
    $target_percent = ($total_revenue / $sales_target) * 100;
}

// 4. ຂໍ້ມູນກຣາຟ ( Admin Only )
$line_labels = []; $line_data = []; $status_labels = []; $status_data = []; $top_tour_names = []; $top_tour_counts = [];
if (isAdmin()) {
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $line_labels[] = date('d/m', strtotime($d));
        $val = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as t FROM payments WHERE DATE(payment_date) = '$d'"))['t'];
        $line_data[] = (float)($val ?? 0);
    }
    $st_query = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
    while($st = mysqli_fetch_assoc($st_query)) {
        $status_labels[] = ($st['status'] == 'Confirmed') ? 'ອະນຸມັດ' : (($st['status'] == 'Cancelled') ? 'ຍົກເລີກ' : 'ລໍຖ້າ');
        $status_data[] = $st['count'];
    }
    $top_query = mysqli_query($conn, "SELECT t.tour_name, COUNT(b.booking_id) as total FROM tours t JOIN bookings b ON t.tour_id = b.tour_id GROUP BY t.tour_id ORDER BY total DESC LIMIT 5");
    while($top = mysqli_fetch_assoc($top_query)) { $top_tour_names[] = $top['tour_name']; $top_tour_counts[] = $top['total']; }
}
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <!-- Header & Filter -->
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <div>
                <h2 class="fw-bold text-dark mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Dashboard ພາບລວມທຸລະກິດ</h2>
                <p class="text-muted small mb-0">ລາຍງານປະສິດທິພາບ | ສິດ: <b><?php echo $_SESSION['role']; ?></b></p>
            </div>
            <div class="btn-group shadow-sm p-1 bg-white rounded-pill">
                <a href="?range=today" class="btn rounded-pill px-3 <?php echo ($range == 'today') ? 'btn-primary shadow' : 'btn-light'; ?> small">ມື້ນີ້</a>
                <a href="?range=week" class="btn rounded-pill px-3 <?php echo ($range == 'week') ? 'btn-primary shadow' : 'btn-light'; ?> small">7 ວັນ</a>
                <a href="?range=month" class="btn rounded-pill px-3 <?php echo ($range == 'month') ? 'btn-primary shadow' : 'btn-light'; ?> small">ເດືອນນີ້</a>
            </div>
        </div>

        <!-- Section 1: 5 Summary Boxes ( Everyone Sees ) -->
        <div class="row g-3 mb-4 text-center">
            <div class="col"><div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-primary mb-1"><i class="fas fa-calendar-check"></i></div>
                <small class="text-muted d-block small">ການຈອງ</small><h4 class="fw-bold mb-0"><?php echo number_format($c_book); ?></h4>
            </div></div>
            <div class="col"><div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-info mb-1"><i class="fas fa-map-marked-alt"></i></div>
                <small class="text-muted d-block small">ແພັກເກັດ</small><h4 class="fw-bold mb-0"><?php echo number_format($c_tour); ?></h4>
            </div></div>
            <div class="col"><div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-success mb-1"><i class="fas fa-user-tie"></i></div>
                <small class="text-muted d-block small">ໃຫ້ບໍລິການ</small><h4 class="fw-bold mb-0"><?php echo number_format($c_guide); ?></h4>
            </div></div>
            <div class="col"><div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-danger mb-1"><i class="fas fa-ticket-alt"></i></div>
                <small class="text-muted d-block small">ຄູປອງ</small><h4 class="fw-bold mb-0"><?php echo number_format($c_coupon); ?></h4>
            </div></div>
            <div class="col"><div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-warning mb-1"><i class="fas fa-users"></i></div>
                <small class="text-muted d-block small">ລູກຄ້າ</small><h4 class="fw-bold mb-0"><?php echo number_format($c_cust); ?></h4>
            </div></div>
        </div>

        <?php if(isAdmin()): ?>
        <!-- Section 2: Financials ( Admin Only ) -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between mb-3"><h6 class="text-muted fw-bold">ລາຍຮັບສຸດທິ</h6><div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary"><i class="fas fa-wallet"></i></div></div>
                    <h2 class="fw-bold mb-1">₭ <?php echo number_format($total_revenue); ?></h2>
                    <div class="small">
                        <span class="<?php echo ($rev_growth >= 0) ? 'text-success' : 'text-danger'; ?> fw-bold">
                            <i class="fas fa-arrow-<?php echo ($rev_growth >= 0) ? 'up' : 'down'; ?> me-1"></i> <?php echo round(abs($rev_growth), 1); ?>%
                        </span>
                        <span class="text-muted ms-1">ທຽບກັບຊ່ວງກ່ອນ</span>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3"><h6 class="text-muted fw-bold mb-0">ເປົ້າໝາຍຍອດຂາຍເດືອນນີ້</h6><span class="badge bg-light text-dark border fw-normal small">ເປົ້າໝາຍ: 100M</span></div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="progress flex-grow-1" style="height: 15px; border-radius: 50px;"><div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: <?php echo min($target_percent, 100); ?>%"></div></div>
                        <h4 class="fw-bold mb-0 text-success"><?php echo round($target_percent, 1); ?>%</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Charts ( Admin Only ) -->
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <h6 class="fw-bold mb-4">ແນວໂນ້ມການຈ່າຍເງິນ (7 ວັນຫຼ້າສຸດ)</h6>
                    <div style="height: 300px;"><canvas id="revenueLineChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white text-center">
                    <h6 class="fw-bold mb-4">ສັດສ່ວນສະຖານະການຈອງ</h6>
                    <div style="height: 250px;"><canvas id="statusPieChart"></canvas></div>
                    <div class="mt-3 small d-flex justify-content-center gap-2">
                        <span style="color:#198754">● ສຳເລັດ</span> <span style="color:#dc3545">● ຍົກເລີກ</span> <span style="color:#ffc107">● ລໍຖ້າ</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Section 4: Recent List ( Everyone Sees ) -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white py-3 border-0"><h6 class="fw-bold mb-0">ລາຍການຈອງຫຼ້າສຸດ</h6></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="bg-light small"><tr><th>ລູກຄ້າ</th><th>ແພັກເກັດ</th><th class="text-end">ລາຄາລວມ</th><th>ສະຖານະ</th></tr></thead>
                    <tbody>
                        <?php 
                        $recent = mysqli_query($conn, "SELECT b.*, c.fullname, t.tour_name FROM bookings b JOIN customers c ON b.customer_id=c.customer_id JOIN tours t ON b.tour_id=t.tour_id ORDER BY b.booking_id DESC LIMIT 5");
                        while($r = mysqli_fetch_assoc($recent)): 
                            $st = $r['status']; $badge = ($st=='Confirmed') ? 'bg-success' : (($st=='Cancelled') ? 'bg-danger' : 'bg-warning text-dark');
                        ?>
                        <tr>
                            <td class="small"><b><?php echo $r['fullname']; ?></b></td>
                            <td class="small text-truncate" style="max-width: 150px;"><?php echo $r['tour_name']; ?></td>
                            <td class="text-end fw-bold text-danger small">₭ <?php echo number_format($r['total_price']); ?></td>
                            <td><span class="badge rounded-pill <?php echo $badge; ?>" style="font-size: 0.6rem;"><?php echo ($st=='Confirmed')?'ສຳເລັດ':(($st=='Cancelled')?'ຍົກເລີກ':'ລໍຖ້າ'); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php if(isAdmin()): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('revenueLineChart').getContext('2d'), {
        type: 'line',
        data: { labels: <?php echo json_encode($line_labels); ?>, datasets: [{ label: 'ລາຍຮັບ', data: <?php echo json_encode($line_data); ?>, borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', fill: true, tension: 0.4, borderWidth: 3, pointRadius: 5 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' }, ticks: { callback: v => v.toLocaleString() + ' ₭' } } } }
    });
    new Chart(document.getElementById('statusPieChart').getContext('2d'), {
        type: 'doughnut',
        data: { labels: <?php echo json_encode($status_labels); ?>, datasets: [{ data: <?php echo json_encode($status_data); ?>, backgroundColor: ['#198754', '#dc3545', '#ffc107'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
    });
</script>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>