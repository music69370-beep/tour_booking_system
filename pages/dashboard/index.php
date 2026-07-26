<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// --- 1. ດຶງຂໍ້ມູນສະຫຼຸບຊັບພະຍາກອນ (Resources) ---
$c_tour = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tours"))['c'] ?? 0;

// ແກ້ໄຂຈຳນວນໄກ້: ໃຫ້ນັບທຸກຄົນໃນຕາຕະລາງ guides
$c_guide = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM guides"))['c'] ?? 0;

$c_driver = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM drivers"))['c'] ?? 0;
$c_vehicle = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM vehicles"))['c'] ?? 0;
$c_cust = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM customers"))['c'] ?? 0;
$c_book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings"))['c'] ?? 0;

// --- 2. ຂໍ້ມູນແຈ້ງເຕືອນວຽກດ່ວນ (Smart Alerts) ---
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE status='Pending'"))['c'] ?? 0;
$confirmed_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE status='Confirmed'"))['c'] ?? 0; // ເພີ່ມໃໝ່

$tomorrow = date('Y-m-d', strtotime('+1 day'));
$upcoming_tomorrow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as c FROM bookings WHERE travel_date='$tomorrow' AND status='Confirmed'"))['c'] ?? 0;

$exp_driver = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM drivers WHERE license_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))['c'] ?? 0;
$exp_vehicle = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM vehicles WHERE insurance_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))['c'] ?? 0;
$total_alerts = $exp_driver + $exp_vehicle;

// --- 3. ການເງິນ ---
$range = isset($_GET['range']) ? $_GET['range'] : 'month';
$start_date = ($range == 'today') ? date('Y-m-d') : (($range == 'week') ? date('Y-m-d', strtotime("-7 days")) : date('Y-m-01'));

$rev_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as confirmed_rev FROM bookings WHERE status = 'Confirmed' AND DATE(booking_date) >= '$start_date'"));
$total_revenue = (float)($rev_res['confirmed_rev'] ?? 0);
$exp_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_exp FROM tour_expenses WHERE travel_date >= '$start_date'"));
$total_expense = (float)($exp_res['total_exp'] ?? 0);
$net_profit = $total_revenue - $total_expense;

$sales_target = 100000000; 
$target_percent = ($total_revenue > 0) ? ($total_revenue / $sales_target) * 100 : 0;

// --- 4. ຂໍ້ມູນກຣາຟ (Charts) ---
$line_labels = []; $line_data = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $line_labels[] = date('d/m', strtotime($d));
    $val = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as t FROM bookings WHERE status = 'Confirmed' AND DATE(booking_date) = '$d'"))['t'];
    $line_data[] = (float)($val ?? 0);
}

// ຂໍ້ມູນກຣາຟສະຖານະການຈອງ
$st_labels = []; $st_data = [];
$st_query = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
while($st = mysqli_fetch_assoc($st_query)) {
    $l = $st['status'];
    $label = ($l == 'Confirmed') ? 'ອະນຸມັດແລ້ວ' : (($l == 'Cancelled') ? 'ຍົກເລີກແລ້ວ' : 'ລໍຖ້າອະນຸມັດ');
    $st_labels[] = $label;
    $st_data[] = $st['count'];
}

// ຂໍ້ມູນກຣາຟສັດສ່ວນລາຍຈ່າຍ
$cat_labels = []; $cat_data = [];
$cat_map = ['Fuel'=>'ຄ່ານ້ຳມັນ','Hotel'=>'ທີ່ພັກ','Maintenance'=>'ສ້ອມແປງ','Food'=>'ອາຫານ','Guide_Fee'=>'ຄ່າໄກ້','Other'=>'ອື່ນໆ'];
$exp_cat_query = mysqli_query($conn, "SELECT category, SUM(amount) as total FROM tour_expenses GROUP BY category");
while($ec = mysqli_fetch_assoc($exp_cat_query)) {
    $cat_labels[] = $cat_map[$ec['category']] ?? $ec['category'];
    $cat_data[] = $ec['total'];
}
?>

<style>
    .main-content { background-color: #f4f7f6; }
    .stat-card { border: none; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .resource-card { border: none; border-radius: 15px; background: #fff; padding: 15px; display: flex; align-items: center; gap: 12px; height: 100%; }
    .resource-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .alert-card { border: none; border-radius: 15px; background: #fff; padding: 12px 20px; display: flex; align-items: center; gap: 15px; border-left: 5px solid #0d6efd; transition: 0.3s; height: 100%; }
    .review-item { border-left: 3px solid #ffc107; background: #fffcf0; padding: 12px; border-radius: 12px; margin-bottom: 12px; border: 1px solid #eee; }
    .top-tour-card { border: none; border-radius: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-bottom: 10px; }
    .table-custom thead th { background: #f8f9fa; border: none; color: #888; font-size: 0.75rem; text-transform: uppercase; }
    .badge-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 py-4">
        <!-- 1. Smart Alerts Bar (ເພີ່ມ ອະນຸມັດແລ້ວ ເຂົ້າໄປ) -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="alert-card shadow-sm border-warning">
                    <div class="fs-4 text-warning"><i class="fas fa-clock"></i></div>
                    <div><h6 class="fw-bold mb-0"><?php echo $pending_count; ?> ລາຍການ</h6><small class="text-muted">ລໍຖ້າອະນຸມັດ</small></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert-card shadow-sm border-success">
                    <div class="fs-4 text-success"><i class="fas fa-check-circle"></i></div>
                    <div><h6 class="fw-bold mb-0"><?php echo $confirmed_count; ?> ລາຍການ</h6><small class="text-muted">ອະນຸມັດແລ້ວ</small></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert-card shadow-sm border-primary">
                    <div class="fs-4 text-primary"><i class="fas fa-plane-departure"></i></div>
                    <div><h6 class="fw-bold mb-0"><?php echo $upcoming_tomorrow ?: 0; ?> ຄົນ</h6><small class="text-muted">ເດີນທາງມື້ອື່ນ</small></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert-card shadow-sm border-danger">
                    <div class="fs-4 text-danger"><i class="fas fa-file-invoice"></i></div>
                    <div><h6 class="fw-bold mb-0"><?php echo $total_alerts; ?> ລາຍການ</h6><small class="text-muted">ເອກະສານໃກ້ໝົດອາຍຸ</small></div>
                </div>
            </div>
        </div>

        <!-- 2. System Resources Widgets -->
        <div class="row g-3 mb-4">
            <div class="col-md-2"><div class="resource-card shadow-sm"><div class="resource-icon bg-primary text-white"><i class="fas fa-map"></i></div><div><small class="text-muted d-block small">ແພັກເກັດ</small><h5 class="fw-bold mb-0"><?php echo $c_tour; ?></h5></div></div></div>
            <div class="col-md-2"><div class="resource-card shadow-sm"><div class="resource-icon bg-info text-white"><i class="fas fa-user-tie"></i></div><div><small class="text-muted d-block small">ໄກ້ທັງໝົດ</small><h5 class="fw-bold mb-0"><?php echo $c_guide; ?></h5></div></div></div>
            <div class="col-md-2"><div class="resource-card shadow-sm"><div class="resource-icon bg-success text-white"><i class="fas fa-user-check"></i></div><div><small class="text-muted d-block small">ຄົນຂັບ</small><h5 class="fw-bold mb-0"><?php echo $c_driver; ?></h5></div></div></div>
            <div class="col-md-2"><div class="resource-card shadow-sm"><div class="resource-icon bg-warning text-white"><i class="fas fa-bus"></i></div><div><small class="text-muted d-block small">ລົດທົວ</small><h5 class="fw-bold mb-0"><?php echo $c_vehicle; ?></h5></div></div></div>
            <div class="col-md-2"><div class="resource-card shadow-sm"><div class="resource-icon bg-dark text-white"><i class="fas fa-users"></i></div><div><small class="text-muted d-block small">ລູກຄ້າ</small><h5 class="fw-bold mb-0"><?php echo $c_cust; ?></h5></div></div></div>
            <div class="col-md-2"><div class="resource-card shadow-sm border-start border-primary border-4"><div class="resource-icon bg-light text-primary"><i class="fas fa-calendar-check"></i></div><div><small class="text-muted d-block small">ຈອງລວມ</small><h5 class="fw-bold mb-0"><?php echo $c_book; ?></h5></div></div></div>
        </div>

        <!-- 3. Financial Totals -->
        <div class="row g-4 mb-4">
            <div class="col-md-4"><div class="card stat-card p-4 bg-white border-start border-primary border-5"><small class="text-muted fw-bold">ລາຍຮັບ</small><h2 class="fw-bold text-primary mb-0">₭ <?php echo number_format($total_revenue); ?></h2></div></div>
            <div class="col-md-4"><div class="card stat-card p-4 bg-white border-start border-danger border-5"><small class="text-muted fw-bold">ລາຍຈ່າຍ</small><h2 class="fw-bold text-danger mb-0">₭ <?php echo number_format($total_expense); ?></h2></div></div>
            <div class="col-md-4"><div class="card stat-card p-4 <?php echo ($net_profit >= 0) ? 'bg-success text-white' : 'bg-danger text-white'; ?> shadow-lg"><small class="opacity-75 fw-bold">ກຳໄລສຸດທິ</small><h2 class="fw-bold mb-0">₭ <?php echo number_format($net_profit); ?></h2></div></div>
        </div>

        <!-- 4. Revenue Trend & Visual Analytics -->
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h6 class="fw-bold mb-4">ແນວໂນ້ມລາຍຮັບ 7 ວັນ</h6>
                    <div style="height: 300px;"><canvas id="revenueLineChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h6 class="fw-bold mb-4 text-center">ບົດວິເຄາະການເງິນ</h6>
                    <div style="height: 250px;"><canvas id="profitComparisonChart"></canvas></div>
                    <div class="text-center mt-3 small">
                        <span class="badge bg-primary rounded-pill me-2">ລາຍຮັບ</span>
                        <span class="badge bg-danger rounded-pill">ລາຍຈ່າຍ</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Sales Target -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between mb-2"><h6 class="fw-bold mb-0">ຄວາມຄືບໜ້າຍອດຂາຍ (ເປົ້າໝາຍ 100 ລ້ານ)</h6><span class="fw-bold text-primary"><?php echo round($target_percent, 1); ?>%</span></div>
            <div class="progress" style="height:12px; border-radius:50px; background:#eee;"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo min($target_percent, 100); ?>%"></div></div>
        </div>

        <!-- 6. Bottom Multi-Grid (ສ່ວນທີ່ເຈົ້າບອກວ່າຫາຍໄປ - ຂ້ອຍເອົາມາຄືນໃຫ້ແລ້ວ) -->
        <div class="row g-4">
            <!-- Left: Top Selling & Reviews -->
            <div class="col-lg-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-trophy text-warning me-2"></i>ແພັກເກັດຂາຍດີ</h6>
                <?php 
                $top_sql = "SELECT t.tour_name, COUNT(b.booking_id) as total FROM tours t JOIN bookings b ON t.tour_id=b.tour_id WHERE b.status='Confirmed' GROUP BY t.tour_id ORDER BY total DESC LIMIT 2";
                $top_res = mysqli_query($conn, $top_sql); $rank = 1;
                while($top = mysqli_fetch_assoc($top_res)): ?>
                <div class="card top-tour-card p-3 shadow-sm"><div class="d-flex align-items-center gap-3"><h4 class="mb-0 opacity-50">#<?php echo $rank++; ?></h4><div><h6 class="mb-0 fw-bold"><?php echo $top['tour_name']; ?></h6><small class="opacity-75"><?php echo $top['total']; ?> ຈອງ</small></div></div></div>
                <?php endwhile; ?>

                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mt-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-star text-warning me-2"></i>ຄຳຊົມເຊີຍລູກຄ້າ</h6>
                    <?php 
                    $rev_sql = "SELECT r.*, c.fullname FROM reviews r JOIN customers c ON r.customer_id=c.customer_id ORDER BY r.review_id DESC LIMIT 2";
                    $rev_res = mysqli_query($conn, $rev_sql);
                    while($rv = mysqli_fetch_assoc($rev_res)): ?>
                    <div class="review-item small"><div class="d-flex justify-content-between"><b><?php echo $rv['fullname']; ?></b> <span class="text-warning">★ <?php echo $rv['rating']; ?></span></div><p class="mb-0 text-muted">"<?php echo $rv['comment']; ?>"</p></div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Recent Bookings Table -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center"><h6 class="fw-bold mb-0">ການຈອງຫຼ້າສຸດ</h6><a href="../bookings/index.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">ທັງໝົດ</a></div>
                    <div class="table-responsive">
                        <table class="table table-custom mb-0 align-middle">
                            <thead><tr><th class="ps-4">ລູກຄ້າ</th><th>ແພັກເກັດ</th><th>ສະຖານະ</th></tr></thead>
                            <tbody>
                                <?php 
                                $rec_sql = "SELECT b.*, c.fullname, t.tour_name FROM bookings b JOIN customers c ON b.customer_id=c.customer_id JOIN tours t ON b.tour_id=t.tour_id ORDER BY b.booking_id DESC LIMIT 5";
                                $rec_res = mysqli_query($conn, $rec_sql);
                                while($r = mysqli_fetch_assoc($rec_res)):
                                    $s = $r['status'];
                                    $s_lao = ($s=='Confirmed')?'ອະນຸມັດ':(($s=='Cancelled')?'ຍົກເລີກ':'ລໍຖ້າ');
                                    $dot = ($s=='Confirmed')?'bg-success':(($s=='Cancelled')?'bg-danger':'bg-warning');
                                ?>
                                <tr>
                                    <td class="ps-4"><b><?php echo $r['fullname']; ?></b><br><small class="text-muted"><?php echo date('d/m', strtotime($r['booking_date'])); ?></small></td>
                                    <td class="small text-truncate" style="max-width: 100px;"><?php echo $r['tour_name']; ?></td>
                                    <td><span class="badge-dot <?php echo $dot; ?>"></span> <small><?php echo $s_lao; ?></small></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Booking Status Pie -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 text-center">
                    <h6 class="fw-bold mb-4">ສະຖານະການຈອງ</h6>
                    <div style="height: 180px;"><canvas id="statusDoughnutChart"></canvas></div>
                    <div class="mt-4 small d-flex flex-column gap-2 text-start">
                        <div class="d-flex align-items-center"><span class="badge-dot bg-success"></span> ອະນຸມັດແລ້ວ</div>
                        <div class="d-flex align-items-center"><span class="badge-dot bg-warning"></span> ລໍຖ້າອະນຸມັດ</div>
                        <div class="d-flex align-items-center"><span class="badge-dot bg-danger"></span> ຍົກເລີກແລ້ວ</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart
    new Chart(document.getElementById('revenueLineChart'), { type: 'line', data: { labels: <?php echo json_encode($line_labels); ?>, datasets: [{ label: 'ລາຍຮັບ', data: <?php echo json_encode($line_data); ?>, borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,0.05)', fill: true, tension: 0.4 }] }, options: { responsive: true, maintainAspectRatio: false } });
    
    // Status Pie Chart
    new Chart(document.getElementById('statusDoughnutChart'), { type: 'doughnut', data: { labels: <?php echo json_encode($st_labels); ?>, datasets: [{ data: <?php echo json_encode($st_data); ?>, backgroundColor: ['#198754', '#dc3545', '#ffc107'] }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '80%', plugins: { legend: { display: false } } } });

    // Profit Comparison Donut
    new Chart(document.getElementById('profitComparisonChart'), { type: 'doughnut', data: { labels: ['ລາຍຮັບ', 'ລາຍຈ່າຍ'], datasets: [{ data: [<?php echo $total_revenue; ?>, <?php echo $total_expense; ?>], backgroundColor: ['#0d6efd', '#dc3545'] }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } } });
</script>

<?php include '../../includes/footer.php'; ?>