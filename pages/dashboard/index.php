<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// ຟັງຊັນຊ່ວຍກວດສອບ Query
function safe_query($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return 0; // ຖ້າ Error ໃຫ້ສົ່ງຄ່າ 0 ໄປກ່ອນ
    }
    return $result;
}

// 1. ດຶງຂໍ້ມູນສະຖິຕິພື້ນຖານ
$tours_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tours"))['total'] ?? 0;
$customers_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM customers"))['total'] ?? 0;
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status='Pending'"))['total'] ?? 0;

// 2. ຄຳນວນລາຍຮັບທັງໝົດ
$revenue_query = mysqli_query($conn, "SELECT SUM(amount) as total FROM payments");
$total_revenue = ($revenue_query) ? mysqli_fetch_assoc($revenue_query)['total'] : 0;
$total_revenue = $total_revenue ?? 0;

// 3. ຄຳນວນຕົ້ນທຶນທັງໝົດ (ກວດສອບ Column ກ່ອນ)
$cost_sql = "SELECT SUM(b.num_people * t.cost_per_person) as total_cost 
             FROM bookings b 
             JOIN tours t ON b.tour_id = t.tour_id 
             WHERE b.status = 'Confirmed'";
$cost_query = mysqli_query($conn, $cost_sql);
$total_cost = ($cost_query) ? mysqli_fetch_assoc($cost_query)['total_cost'] : 0;
$total_cost = $total_cost ?? 0;

// 4. ກຳໄລສຸດທິ
$net_profit = $total_revenue - $total_cost;

// 5. ດຶງຂໍ້ມູນກຣາຟລາຍຮັບ (7 ມື້ຢ້ອນຫຼັງ)
$chart_labels = [];
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $display_date = date('d/m', strtotime($date));
    $chart_labels[] = $display_date;
    
    $day_rev_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM payments WHERE DATE(payment_date) = '$date'"));
    $chart_data[] = (float)($day_rev_res['total'] ?? 0);
}

// 6. ດຶງຂໍ້ມູນທົວຂາຍດີ (Top 5)
$tour_names = [];
$tour_bookings = [];
$pop_query = mysqli_query($conn, "
    SELECT t.tour_name, COUNT(b.booking_id) as count 
    FROM tours t 
    LEFT JOIN bookings b ON t.tour_id = b.tour_id 
    GROUP BY t.tour_id 
    ORDER BY count DESC LIMIT 5
");
if($pop_query) {
    while($row = mysqli_fetch_assoc($pop_query)) {
        $tour_names[] = $row['tour_name'];
        $tour_bookings[] = (int)$row['count'];
    }
}
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-th-large text-primary me-2"></i>ແຜງຄວບຄຸມ (Dashboard)</h2>
            <div class="text-muted small">ວັນທີ: <?php echo date('d/m/Y'); ?></div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary text-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-wallet fa-lg"></i></div>
                        <div><p class="mb-0 small opacity-75">ລາຍຮັບ</p><h4 class="fw-bold mb-0"><?php echo number_format($total_revenue); ?></h4></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-danger text-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-hand-holding-usd fa-lg"></i></div>
                        <div><p class="mb-0 small opacity-75">ຕົ້ນທຶນ</p><h4 class="fw-bold mb-0"><?php echo number_format($total_cost); ?></h4></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-success text-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-chart-line fa-lg"></i></div>
                        <div><p class="mb-0 small opacity-75">ກຳໄລ</p><h4 class="fw-bold mb-0"><?php echo number_format($net_profit); ?></h4></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-warning text-dark h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3"><i class="fas fa-clock fa-lg"></i></div>
                        <div><p class="mb-0 small opacity-75">ລໍຖ້າອະນຸມັດ</p><h4 class="fw-bold mb-0"><?php echo $pending_count; ?></h4></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4">ແນວໂນ້ມລາຍຮັບ 7 ວັນຫຼ້າສຸດ (ກີບ)</h5>
                    <div style="height: 350px;"><canvas id="revenueChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center">
                    <h5 class="fw-bold mb-4">ອັດຕາກຳໄລ (Margin)</h5>
                    <div class="py-5">
                        <?php $margin = ($total_revenue > 0) ? ($net_profit / $total_revenue) * 100 : 0; ?>
                        <h1 class="display-2 fw-bold text-success"><?php echo round($margin, 1); ?>%</h1>
                        <p class="text-muted">ກຳໄລສຸດທິ</p>
                    </div>
                    <div style="height: 180px;"><canvas id="tourPieChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-0"><h5 class="fw-bold mb-0 text-dark">ລາຍການຈອງຫຼ້າສຸດ</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr><th>ວັນທີຈອງ</th><th>ລູກຄ້າ</th><th>ທົວ</th><th>ລາຄາລວມ</th><th>ສະຖານະ</th></tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res = mysqli_query($conn, "SELECT b.*, c.fullname, t.tour_name FROM bookings b JOIN customers c ON b.customer_id=c.customer_id JOIN tours t ON b.tour_id=t.tour_id ORDER BY b.booking_id DESC LIMIT 5");
                            if($res) {
                                while($row = mysqli_fetch_assoc($res)):
                            ?>
                                <tr>
                                    <td class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($row['booking_date'])); ?></td>
                                    <td class="fw-bold"><?php echo $row['fullname']; ?></td>
                                    <td><?php echo $row['tour_name']; ?></td>
                                    <td class="text-danger fw-bold"><?php echo number_format($row['total_price']); ?></td>
                                    <td><span class="badge rounded-pill <?php echo ($row['status']=='Confirmed') ? 'bg-success' : 'bg-warning text-dark'; ?>"><?php echo ($row['status']=='Confirmed') ? 'ຢືນຢັນແລ້ວ' : 'ລໍຖ້າອະນຸມັດ'; ?></span></td>
                                </tr>
                            <?php endwhile; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxLine = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'ລາຍຮັບ',
                data: <?php echo json_encode($chart_data); ?>,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 5,
                pointBackgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return value.toLocaleString(); } } } }
        }
    });

    const ctxPie = document.getElementById('tourPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($tour_names); ?>,
            datasets: [{
                data: <?php echo json_encode($tour_bookings); ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#858796'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
    });
</script>

<?php include '../../includes/footer.php'; ?>