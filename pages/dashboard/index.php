<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// 1. ດຶງຂໍ້ມູນສະຖິຕິເບື້ອງຕົ້ນ
$tours_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tours"))['total'] ?? 0;
$customers_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM customers"))['total'] ?? 0;
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status='Pending'"))['total'] ?? 0;

// 2. ຄຳນວນລາຍຮັບ (Revenue): ເງິນທີ່ຮັບມາທັງໝົດ - ເງິນທີ່ຄືນໃຫ້ລູກຄ້າ (Refund)
$rev_data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
    (SELECT SUM(amount) FROM payments) as total_paid,
    (SELECT SUM(refund_amount) FROM bookings WHERE status='Cancelled') as total_refund
"));
$total_revenue = ($rev_data['total_paid'] ?? 0) - ($rev_data['total_refund'] ?? 0);

// 3. ຄຳນວນຕົ້ນທຶນ (Total Cost): ຕົ້ນທຶນທົວທີ່ໄປແທ້ + ຕົ້ນທຶນທີ່ເສຍໄປຍ້ອນການຍົກເລີກ (Cancel Cost)
$cost_data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
    (SELECT SUM(b.num_people * t.cost_per_person) FROM bookings b JOIN tours t ON b.tour_id = t.tour_id WHERE b.status = 'Confirmed') as active_tour_cost,
    (SELECT SUM(cancellation_cost) FROM bookings WHERE status = 'Cancelled') as cancel_lost_cost
"));
$total_cost = ($cost_data['active_tour_cost'] ?? 0) + ($cost_data['cancel_lost_cost'] ?? 0);

// 4. ກຳໄລສຸດທິ
$net_profit = $total_revenue - $total_cost;

// 5. ຂໍ້ມູນກຣາຟລາຍຮັບ 7 ມື້
$chart_labels = []; $chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('d/m', strtotime($date));
    $rev = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM payments WHERE DATE(payment_date) = '$date'"));
    $chart_data[] = (float)($rev['total'] ?? 0);
}

// 6. ທົວຂາຍດີ
$tour_names = []; $tour_bookings = [];
$pop_query = mysqli_query($conn, "SELECT t.tour_name, COUNT(b.booking_id) as count FROM tours t LEFT JOIN bookings b ON t.tour_id = b.tour_id GROUP BY t.tour_id ORDER BY count DESC LIMIT 5");
while($p = mysqli_fetch_assoc($pop_query)) { $tour_names[] = $p['tour_name']; $tour_bookings[] = (int)$p['count']; }
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-chart-pie text-primary me-2"></i>ລາຍງານຜົນປະກອບການ</h2>
            <div class="text-muted small">ສະຫຼຸບຂໍ້ມູນວັນທີ: <?php echo date('d/m/Y H:i'); ?></div>
        </div>

        <!-- Financial Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white">
                    <h6 class="opacity-75 small">ລາຍຮັບສຸດທິ (Net Revenue)</h6>
                    <h2 class="fw-bold mb-0"><?php echo number_format($total_revenue); ?> <small class="fs-6">ກີບ</small></h2>
                    <small class="opacity-50">ຫັກລົບຍອດຄືນເງິນ (Refund) ແລ້ວ</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-danger text-white">
                    <h6 class="opacity-75 small">ຕົ້ນທຶນລວມ (Total Cost)</h6>
                    <h2 class="fw-bold mb-0"><?php echo number_format($total_cost); ?> <small class="fs-6">ກີບ</small></h2>
                    <small class="opacity-50">ລວມຕົ້ນທຶນທີ່ເສຍຈາກການຍົກເລີກ</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-success text-white">
                    <h6 class="opacity-75 small">ກຳໄລສຸດທິ (Net Profit)</h6>
                    <h2 class="fw-bold mb-0"><?php echo number_format($net_profit); ?> <small class="fs-6">ກີບ</small></h2>
                    <small class="opacity-50">ກຳໄລແທ້ໆ ຫຼັງຫັກຄ່າໃຊ້ຈ່າຍທັງໝົດ</small>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4">ແນວໂນ້ມລາຍຮັບ 7 ມື້ (ກີບ)</h5>
                    <div style="height: 350px;"><canvas id="revenueChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center">
                    <h5 class="fw-bold mb-4">ອັດຕາກຳໄລ (Margin)</h5>
                    <div class="py-5">
                        <?php $margin = ($total_revenue > 0) ? ($net_profit / $total_revenue) * 100 : 0; ?>
                        <h1 class="display-1 fw-bold text-success"><?php echo round($margin, 1); ?>%</h1>
                        <p class="text-muted">ກຳໄລສະເລ່ຍຕໍ່ຍອດຂາຍ</p>
                    </div>
                    <div style="height: 150px;"><canvas id="tourPieChart"></canvas></div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-0"><h5 class="fw-bold mb-0">ການຈອງຫຼ້າສຸດ</h5></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small">
                        <tr><th class="ps-4">ວັນທີຈອງ</th><th>ລູກຄ້າ</th><th>ແພັກເກັດທົວ</th><th class="text-end">ລາຄາລວມ</th><th class="text-center">ສະຖານະ</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = mysqli_query($conn, "SELECT b.*, c.fullname, t.tour_name FROM bookings b JOIN customers c ON b.customer_id=c.customer_id JOIN tours t ON b.tour_id=t.tour_id ORDER BY b.booking_id DESC LIMIT 5");
                        while($r = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td class="ps-4 small text-muted"><?php echo date('d/m/Y H:i', strtotime($r['booking_date'])); ?></td>
                                <td class="fw-bold"><?php echo $r['fullname']; ?></td>
                                <td class="small"><?php echo $r['tour_name']; ?></td>
                                <td class="text-end fw-bold text-danger"><?php echo number_format($r['total_price']); ?></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?php echo ($r['status']=='Confirmed')?'bg-success':(($r['status']=='Cancelled')?'bg-danger':'bg-warning text-dark'); ?>">
                                        <?php echo ($r['status']=='Confirmed')?'ຢືນຢັນແລ້ວ':(($r['status']=='Cancelled')?'ຍົກເລີກ':'ລໍຖ້າອະນຸມັດ'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxL = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxL, {
        type: 'line',
        data: { labels: <?php echo json_encode($chart_labels); ?>, datasets: [{ label: 'ລາຍຮັບ', data: <?php echo json_encode($chart_data); ?>, borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', fill: true, tension: 0.4, borderWidth: 3 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
    const ctxP = document.getElementById('tourPieChart').getContext('2d');
    new Chart(ctxP, {
        type: 'doughnut',
        data: { labels: <?php echo json_encode($tour_names); ?>, datasets: [{ data: <?php echo json_encode($tour_bookings); ?>, backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } }
    });
</script>
<?php include '../../includes/footer.php'; ?>