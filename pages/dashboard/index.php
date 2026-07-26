<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// --- 1. ດຶງຂໍ້ມູນສະຫຼຸບຊັບພະຍາກອນ (Resources) ---
$c_tour = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tours"))['c'] ?? 0;
$c_guide = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM guides"))['c'] ?? 0;
$c_driver = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM drivers"))['c'] ?? 0;
$c_vehicle = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM vehicles"))['c'] ?? 0;
$c_cust = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM customers"))['c'] ?? 0;
$c_book_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings"))['c'] ?? 0;

$c_passengers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as c FROM bookings WHERE status='Confirmed'"))['c'] ?? 0;
$c_room_done = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT booking_id) as c FROM booking_room_assignments"))['c'] ?? 0;
$c_room_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE status='Confirmed' AND booking_id NOT IN (SELECT booking_id FROM booking_room_assignments)"))['c'] ?? 0;

// --- 2. ຂໍ້ມູນແຈ້ງເຕືອນວຽກດ່ວນ (Smart Alerts) ---
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE status='Pending'"))['c'] ?? 0;
$approved_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE status='Confirmed'"))['c'] ?? 0;

$tomorrow = date('Y-m-d', strtotime('+1 day'));
$upcoming_tomorrow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE travel_date='$tomorrow' AND status='Confirmed'"))['c'] ?? 0;
$exp_docs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT (SELECT COUNT(*) FROM drivers WHERE license_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)) + (SELECT COUNT(*) FROM vehicles WHERE insurance_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)) as c"))['c'] ?? 0;

// --- 3. ການເງິນ ---
$range = isset($_GET['range']) ? $_GET['range'] : 'month';
$start_date_filter = ($range == 'today') ? date('Y-m-d') : (($range == 'week') ? date('Y-m-d', strtotime("-7 days")) : date('Y-m-01'));

$rev_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as confirmed_rev FROM bookings WHERE status = 'Confirmed' AND DATE(booking_date) >= '$start_date_filter'"));
$total_revenue = (float)($rev_res['confirmed_rev'] ?? 0);
$exp_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_exp FROM tour_expenses WHERE travel_date >= '$start_date_filter'"));
$total_expense = (float)($exp_res['total_exp'] ?? 0);
$net_profit = $total_revenue - $total_expense;

$sales_target = 100000000; 
$target_percent = ($total_revenue > 0) ? ($total_revenue / $sales_target) * 100 : 0;

// --- 4. ແກ້ໄຂກຣາຟແນວໂນ້ມ (Line Chart) ໃຫ້ສະແດງຂໍ້ມູນຈິງ ---
// --- 4. ແກ້ໄຂກຣາຟແນວໂນ້ມ (Line Chart) ໃຫ້ສະແດງຂໍ້ມູນຈິງ ---
$line_labels = []; $line_data = [];
$data_map = [];

// ຊອກຫາວັນທີທີ່ມີການຈອງຫຼ້າສຸດ ເພື່ອໃຫ້ກຣາຟສະແດງຜົນໄດ້ (ກໍລະນີປ້ອນຂໍ້ມູນປີ 2026)
$max_date_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(DATE(booking_date)) as latest FROM bookings WHERE status='Confirmed'"));
$latest_date = $max_date_res['latest'] ?? date('Y-m-d');

// Query ດຶງຂໍ້ມູນລາຍຮັບ 7 ວັນ ຍ້ອນຫຼັງຈາກວັນທີຫຼ້າສຸດ
$q_trend = mysqli_query($conn, "SELECT DATE(booking_date) as b_date, SUM(total_price) as daily_total 
                                FROM bookings 
                                WHERE status = 'Confirmed' 
                                AND DATE(booking_date) >= DATE_SUB('$latest_date', INTERVAL 6 DAY) 
                                GROUP BY DATE(booking_date)");

while($row_t = mysqli_fetch_assoc($q_trend)) {
    $data_map[$row_t['b_date']] = (float)$row_t['daily_total'];
}

// ສ້າງ Label ແລະ Data 7 ວັນ (ຍ້ອນຫຼັງຈາກວັນທີທີ່ມີຂໍ້ມູນຫຼ້າສຸດ)
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("$latest_date -$i days"));
    $line_labels[] = date('d/m', strtotime($d));
    $line_data[] = isset($data_map[$d]) ? $data_map[$d] : 0;
}

// --- 5. ຂໍ້ມູນກຣາຟວົງມົນ (ສັດສ່ວນລາຍຈ່າຍ) ---
$cat_labels = []; $cat_data = [];
$exp_cat_query = mysqli_query($conn, "SELECT category, SUM(amount) as total FROM tour_expenses GROUP BY category");
while($ec = mysqli_fetch_assoc($exp_cat_query)) {
    $cat_labels[] = $ec['category'];
    $cat_data[] = (float)$ec['total'];
}
?>

<style>
    .main-content { background-color: #f4f7f6; }
    .alert-card { border: none; border-radius: 15px; background: #fff; padding: 15px 20px; display: flex; align-items: center; gap: 15px; border-left: 5px solid #ddd; height: 100%; }
    .resource-card { border: none; border-radius: 15px; background: #fff; padding: 15px; display: flex; align-items: center; gap: 12px; height: 100%; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    .resource-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .stat-card { border: none; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 py-4">
        
        <!-- TOP ALERTS ROW -->
        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <div class="alert-card shadow-sm border-warning">
                    <div class="fs-4 text-warning"><i class="fas fa-clock"></i></div>
                    <div><h6 class="fw-bold mb-0"><?php echo $pending_count; ?> ລາຍການ</h6><small class="text-muted">ລໍຖ້າອະນຸມັດ</small></div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="alert-card shadow-sm border-success">
                    <div class="fs-4 text-success"><i class="fas fa-check-circle"></i></div>
                    <div><h6 class="fw-bold mb-0"><?php echo $approved_count; ?> ລາຍການ</h6><small class="text-muted">ອະນຸມັດແລ້ວ</small></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert-card shadow-sm border-primary">
                    <div class="fs-4 text-primary"><i class="fas fa-plane-departure"></i></div>
                    <div><h6 class="fw-bold mb-0"><?php echo $upcoming_tomorrow; ?> ຄົນ</h6><small class="text-muted">ເດີນທາງມື້ອື່ນ</small></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert-card shadow-sm border-danger">
                    <div class="fs-4 text-danger"><i class="fas fa-file-invoice"></i></div>
                    <div><h6 class="fw-bold mb-0"><?php echo $exp_docs; ?> ລາຍການ</h6><small class="text-muted">ເອກະສານໝົດອາຍຸ</small></div>
                </div>
            </div>
            <div class="col-md-2 text-end d-flex align-items-center justify-content-end">
                <div class="btn-group shadow-sm p-1 bg-white rounded-pill">
                    <a href="?range=today" class="btn rounded-pill px-3 <?php echo ($range == 'today') ? 'btn-primary text-white' : 'btn-light'; ?> border-0 small">ມື້ນີ້</a>
                    <a href="?range=month" class="btn rounded-pill px-3 <?php echo ($range == 'month') ? 'btn-primary text-white' : 'btn-light'; ?> border-0 small">ເດືອນນີ້</a>
                </div>
            </div>
        </div>

        <!-- RESOURCE WIDGETS -->
        <div class="row g-3 mb-4">
            <div class="col-md-2"><div class="resource-card shadow-sm"><div class="resource-icon bg-primary text-white"><i class="fas fa-map"></i></div><div><small class="text-muted d-block small">ແພັກເກັດ</small><h5 class="fw-bold mb-0"><?php echo $c_tour; ?></h5></div></div></div>
            <div class="col-md-2"><div class="resource-card shadow-sm"><div class="resource-icon bg-info text-white"><i class="fas fa-user-tie"></i></div><div><small class="text-muted d-block small">ໃຫ້ບີ້ນທົວ</small><h5 class="fw-bold mb-0"><?php echo $c_guide; ?></h5></div></div></div>
            <div class="col-md-2"><div class="resource-card shadow-sm"><div class="resource-icon bg-success text-white"><i class="fas fa-user-check"></i></div><div><small class="text-muted d-block small">ຄົນຂັບ</small><h5 class="fw-bold mb-0"><?php echo $c_driver; ?></h5></div></div></div>
            <div class="col-md-2"><div class="resource-card shadow-sm"><div class="resource-icon bg-warning text-white"><i class="fas fa-bus"></i></div><div><small class="text-muted d-block small">ລົດທົວ</small><h5 class="fw-bold mb-0"><?php echo $c_vehicle; ?></h5></div></div></div>
            <div class="col-md-2"><div class="resource-card shadow-sm"><div class="resource-icon bg-dark text-white"><i class="fas fa-users"></i></div><div><small class="text-muted d-block small">ລູກຄ້າ</small><h5 class="fw-bold mb-0"><?php echo $c_cust; ?></h5></div></div></div>
            <div class="col-md-2"><div class="resource-card shadow-sm border-start border-primary border-4"><div class="resource-icon bg-light text-primary"><i class="fas fa-calendar-check"></i></div><div><small class="text-muted d-block small">ຈອງລວມ</small><h5 class="fw-bold mb-0"><?php echo $c_book_total; ?></h5></div></div></div>
        </div>

        <!-- SECONDARY STATS -->
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="resource-card shadow-sm border-start border-primary border-4"><div class="resource-icon bg-primary text-white"><i class="fas fa-users"></i></div><div><small class="text-muted d-block small">ຜູ້ໂດຍສານທີ່ຢືນຢັນແລ້ວທັງໝົດ</small><h5 class="fw-bold mb-0"><?php echo $c_passengers; ?> ຄົນ</h5></div></div></div>
            <div class="col-md-4"><div class="resource-card shadow-sm border-start border-success border-4"><div class="resource-icon bg-success text-white"><i class="fas fa-check-circle"></i></div><div><small class="text-muted d-block small">ຈັດສັນຫ້ອງພັກໃຫ້ແລ້ວ</small><h5 class="fw-bold mb-0"><?php echo $c_room_done; ?> ທ່ານ</h5></div></div></div>
            <div class="col-md-4"><div class="resource-card shadow-sm border-start border-danger border-4"><div class="resource-icon bg-danger text-white"><i class="fas fa-bed"></i></div><div><small class="text-muted d-block small">ຍັງບໍ່ໄດ້ຈັດຫ້ອງພັກ</small><h5 class="fw-bold mb-0"><?php echo $c_room_pending; ?> ທ່ານ</h5></div></div></div>
        </div>

        <!-- FINANCIALS -->
        <div class="row g-4 mb-4">
            <div class="col-md-4"><div class="card stat-card p-4 bg-white border-start border-primary border-5"><small class="text-muted fw-bold">ລາຍຮັບ</small><h2 class="fw-bold text-primary mb-0">₭ <?php echo number_format($total_revenue); ?></h2></div></div>
            <div class="col-md-4"><div class="card stat-card p-4 bg-white border-start border-danger border-5"><small class="text-muted fw-bold">ລາຍຈ່າຍ</small><h2 class="fw-bold text-danger mb-0">₭ <?php echo number_format($total_expense); ?></h2></div></div>
            <div class="col-md-4"><div class="card stat-card p-4 <?php echo ($net_profit >= 0) ? 'bg-success text-white' : 'bg-danger text-white'; ?> shadow-lg border-0"><small class="opacity-75 fw-bold">ກຳໄລສຸດທິ</small><h2 class="fw-bold mb-0">₭ <?php echo number_format($net_profit); ?></h2></div></div>
        </div>

        <!-- CHARTS SECTION -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h6 class="fw-bold mb-4"><i class="fas fa-chart-line text-primary me-2"></i>ແນວໂນ້ມລາຍຮັບ 7 ວັນ (Confirm ຕາມວັນທີຈອງ)</h6>
                    <div style="height: 300px;"><canvas id="revenueLineChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h6 class="fw-bold mb-4 text-center">ບົດວິເຄາະການເງິນ</h6>
                    <div class="row">
                        <div class="col-md-6 border-end text-center">
                            <p class="small fw-bold">ລາຍຮັບ vs ລາຍຈ່າຍ</p>
                            <div style="height: 200px;"><canvas id="profitComparisonChart"></canvas></div>
                        </div>
                        <div class="col-md-6 text-center">
                            <p class="small fw-bold">ສັດສ່ວນລາຍຈ່າຍ</p>
                            <div style="height: 200px;"><canvas id="expenseDonutChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SALES TARGET -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between mb-2"><h6 class="fw-bold mb-0">ຄວາມຄືບໜ້າຍອດຂາຍ (ເປົ້າໝາຍ 100 ລ້ານ)</h6><span class="fw-bold text-primary"><?php echo round($target_percent, 1); ?>%</span></div>
            <div class="progress" style="height:12px;"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo min($target_percent, 100); ?>%"></div></div>
        </div>

        <!-- BOTTOM GRID -->
        <div class="row g-4">
            <div class="col-lg-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-trophy text-warning me-2"></i>ແພັກເກັດຂາຍດີ</h6>
                <?php 
                $top_sql = "SELECT t.tour_name, COUNT(b.booking_id) as total FROM tours t JOIN bookings b ON t.tour_id=b.tour_id WHERE b.status='Confirmed' GROUP BY t.tour_id ORDER BY total DESC LIMIT 3";
                $top_res = mysqli_query($conn, $top_sql); $rank = 1;
                while($top = mysqli_fetch_assoc($top_res)): ?>
                <div class="card border-0 p-3 shadow-sm rounded-4 mb-2 bg-white"><div class="d-flex align-items-center gap-3"><h4 class="mb-0 text-primary opacity-50">#<?php echo $rank++; ?></h4><div><h6 class="mb-0 fw-bold"><?php echo $top['tour_name']; ?></h6><small class="text-muted"><?php echo $top['total']; ?> ຈອງ</small></div></div></div>
                <?php endwhile; ?>
            </div>

            <!-- APPROVED LIST TABLE -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-success"><i class="fas fa-check-double me-2"></i>ລາຍການທີ່ອະນຸມັດແລ້ວ (Confirmed)</h6>
                        <a href="../bookings/index.php?status=Confirmed" class="btn btn-sm btn-light border rounded-pill px-3">ເບິ່ງທັງໝົດ</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light small">
                                <tr>
                                    <th class="ps-4">ວັນທີຈອງ</th>
                                    <th>ຊື່ລູກຄ້າ</th>
                                    <th>ແພັກເກັດ</th>
                                    <th class="text-center">ຄົນ</th>
                                    <th class="text-end">ຍອດເງິນ</th>
                                    <th class="text-center">ຈັດການ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $conf_sql = "SELECT b.*, c.fullname, t.tour_name FROM bookings b JOIN customers c ON b.customer_id=c.customer_id JOIN tours t ON b.tour_id=t.tour_id WHERE b.status='Confirmed' ORDER BY b.booking_id DESC LIMIT 5";
                                $conf_res = mysqli_query($conn, $conf_sql);
                                if(mysqli_num_rows($conf_res) > 0):
                                    while($r = mysqli_fetch_assoc($conf_res)):
                                ?>
                                <tr>
                                    <td class="ps-4 small text-muted"><?php echo date('d/m/Y', strtotime($r['booking_date'])); ?></td>
                                    <td class="fw-bold small"><?php echo $r['fullname']; ?></td>
                                    <td class="small text-truncate" style="max-width:150px;"><?php echo $r['tour_name']; ?></td>
                                    <td class="text-center small"><?php echo $r['num_people']; ?></td>
                                    <td class="text-end fw-bold text-primary small"><?php echo number_format($r['total_price']); ?></td>
                                    <td class="text-center"><a href="../bookings/view.php?id=<?php echo $r['booking_id']; ?>" class="btn btn-sm btn-light border rounded-pill"><i class="fas fa-eye"></i></a></td>
                                </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">ຍັງບໍ່ມີລາຍການອະນຸມັດ</td></tr>
                                <?php endif; ?>
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
    // 1. Line Chart ແນວໂນ້ມລາຍຮັບ (ປັບປຸງໃໝ່)
new Chart(document.getElementById('revenueLineChart'), { 
    type: 'line', 
    data: { 
        labels: <?php echo json_encode($line_labels); ?>, 
        datasets: [{ 
            label: 'ລາຍຮັບ (ກີບ)', 
            data: <?php echo json_encode($line_data); ?>, 
            borderColor: '#0d6efd', 
            backgroundColor: 'rgba(13,110,253,0.1)', 
            fill: true, 
            tension: 0.4,
            pointRadius: 6,
            pointBackgroundColor: '#0d6efd',
            borderWidth: 3
        }] 
    }, 
    options: { 
        responsive: true, 
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    // ຟໍແມັດຕົວເລກໃຫ້ມີຈຸດຂັ້ນ (ເຊັ່ນ 1,000,000)
                    callback: function(value) { return value.toLocaleString(); }
                }
            }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) { return 'ລາຍຮັບ: ' + context.parsed.y.toLocaleString() + ' ກີບ'; }
                }
            }
        }
    } 
});
    
    // 2. Profit Comparison
    new Chart(document.getElementById('profitComparisonChart'), { type: 'doughnut', data: { labels: ['ຮັບ', 'ຈ່າຍ'], datasets: [{ data: [<?php echo $total_revenue; ?>, <?php echo $total_expense; ?>], backgroundColor: ['#0d6efd', '#dc3545'] }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } } });

    // 3. Expense Breakdown
    new Chart(document.getElementById('expenseDonutChart'), { type: 'doughnut', data: { labels: <?php echo json_encode($cat_labels); ?>, datasets: [{ data: <?php echo json_encode($cat_data); ?>, backgroundColor: ['#ff6384','#36a2eb','#ffce56','#4bc0c0','#9966ff','#ff9f40'] }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } } });
</script>

<?php include '../../includes/footer.php'; ?>