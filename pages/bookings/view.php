<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

if (!isset($_GET['id'])) { echo "ID missing"; exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

// SQL ດຶງຂໍ້ມູນແບບລະອຽດ
$sql = "SELECT b.*, c.fullname, c.phone, c.email, c.address, 
               t.tour_name, t.tour_code, t.duration, t.meeting_point, t.category, t.meals,
               t.price as price_per_pax, t.cost_per_person, t.highlights, t.whats_included, t.whats_excluded, t.cancellation_policy,
               v.model as car_model, v.plate_number, v.driver_name, v.driver_phone,
               g.fullname as guide_name, g.phone as guide_phone,
               p.amount as paid_amount, p.payment_method, p.payment_slip, p.payment_date
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        LEFT JOIN vehicles v ON t.vehicle_id = v.vehicle_id
        LEFT JOIN guides g ON t.guide_id = g.guide_id
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        WHERE b.booking_id = '$id'";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) { echo "<div class='p-5 text-center'><h3>ບໍ່ພົບຂໍ້ມູນ</h3><a href='index.php'>ກັບຄືນ</a></div>"; exit; }

// ດຶງຂໍ້ມູນ Checklist
$tasks_query = mysqli_query($conn, "SELECT * FROM booking_tasks WHERE booking_id = '$id'");
$total_tasks = mysqli_num_rows($tasks_query);
$done_tasks_res = mysqli_query($conn, "SELECT COUNT(*) as c FROM booking_tasks WHERE booking_id = '$id' AND is_completed = 1");
$done_tasks = mysqli_fetch_assoc($done_tasks_res)['c'];
$percent = ($total_tasks > 0) ? round(($done_tasks / $total_tasks) * 100) : 0;

$total_sale = $row['total_price'];
$total_cost = $row['cost_per_person'] * $row['num_people'];
$profit = $total_sale - $total_cost;
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <!-- Header Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-light border rounded-pill px-3 me-3 shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> ຍ້ອນກັບ
                </a>
                <h2 class="fw-bold mb-0 text-dark">ຈັດການ #BK-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></h2>
            </div>
            <div class="d-flex gap-2">
                <?php if($row['status'] == 'Pending'): ?>
                    <button onclick="confirmApprove(<?php echo $id; ?>, 'approve.php')" class="btn btn-success rounded-pill px-4 shadow-sm">ອະນຸມັດ</button>
                <?php endif; ?>
                <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="fas fa-print"></i> ພິມ</button>
            </div>
        </div>

        <div class="row g-4">
            <!-- ເບື້ອງຊ້າຍ (8/12) -->
            <div class="col-lg-8">
                <!-- 1. ຂໍ້ມູນທົວ ແລະ ລູກຄ້າ -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-4">
                            <div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 mb-2"><?php echo $row['category']; ?></span>
                                <h4 class="fw-bold text-dark mb-1"><?php echo $row['tour_name']; ?></h4>
                                <p class="text-muted small mb-0"> Code: <?php echo $row['tour_code']; ?> | ວັນທີ: <strong class="text-danger"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></strong></p>
                            </div>
                            <span class="badge rounded-pill px-3 py-4 <?php echo ($row['status']=='Confirmed')?'bg-success text-white':'bg-warning text-dark'; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h6 class="fw-bold text-primary mb-2"><i class="fas fa-user-circle me-1"></i> ຜູ້ຈອງ: <?php echo $row['fullname']; ?></h6>
                                <p class="small text-muted mb-1">ເບີໂທ: <?php echo $row['phone']; ?></p>
                                <p class="small text-muted">ທີ່ຢູ່: <?php echo $row['address']; ?></p>
                            </div>
                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold text-primary mb-2"><i class="fas fa-map-marker-alt me-1"></i> ຈຸດນັດພົບ:</h6>
                                <p class="small mb-1">ສະຖານທີ່: <strong><?php echo $row['meeting_point']; ?></strong></p>
                                <p class="small">ຈຳນວນ: <strong><?php echo $row['num_people']; ?> ຄົນ</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ລາຍຊື່ຜູ້ຮ່ວມທາງ -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-users text-info me-2"></i>ລາຍຊື່ຜູ້ຮ່ວມທາງ</h6>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <table class="table table-sm align-middle">
                            <thead class="bg-light">
                                <tr><th class="ps-3" width="50">#</th><th>ຊື່ ແລະ ນາມສະກຸນ</th><th>ເບີໂທ</th></tr>
                            </thead>
                            <tbody>
                                <tr><td class="ps-3">1</td><td><strong><?php echo $row['fullname']; ?></strong> (ຫົວໜ້າ)</td><td><?php echo $row['phone']; ?></td></tr>
                                <?php 
                                $parts = mysqli_query($conn, "SELECT * FROM booking_participants WHERE booking_id = '$id'");
                                $i = 2;
                                while($p = mysqli_fetch_assoc($parts)): ?>
                                    <tr><td class="ps-3"><?php echo $i++; ?></td><td><?php echo $p['participant_name']; ?></td><td><?php echo $p['participant_phone']; ?></td></tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. ສິ່ງທີ່ລວມ/ບໍ່ລວມ -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <h6 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="fas fa-check-circle me-1"></i> ສິ່ງທີ່ລວມຢູ່ນຳ:</h6>
                            <div class="small text-muted" style="white-space: pre-line;"><?php echo $row['whats_included'] ?: '-'; ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <h6 class="fw-bold text-danger border-bottom pb-2 mb-3"><i class="fas fa-times-circle me-1"></i> ສິ່ງທີ່ບໍ່ລວມ:</h6>
                            <div class="small text-muted" style="white-space: pre-line;"><?php echo $row['whats_excluded'] ?: '-'; ?></div>
                        </div>
                    </div>
                </div>

                <!-- *** 4. Checklist ຄວາມພ້ອມ (ຍ້າຍມາໄວ້ບ່ອນນີ້) *** -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white no-print">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                        <i class="fas fa-tasks text-danger me-2"></i>Checklist ຄວາມພ້ອມ ແລະ ການກຽມຕົວ
                    </h6>
                    <div class="row" id="taskList">
                        <?php 
                        if($total_tasks > 0):
                            mysqli_data_seek($tasks_query, 0); 
                            while($t = mysqli_fetch_assoc($tasks_query)): 
                                $new_stat = $t['is_completed'] ? 0 : 1;
                        ?>
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input task-checkbox shadow-none" type="checkbox" 
                                           data-id="<?php echo $t['task_id']; ?>" data-bid="<?php echo $id; ?>"
                                           id="task-<?php echo $t['task_id']; ?>" <?php echo $t['is_completed'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label ms-2 small <?php echo $t['is_completed'] ? 'text-muted text-decoration-line-through' : 'fw-bold'; ?>" 
                                           for="task-<?php echo $t['task_id']; ?>" id="label-<?php echo $t['task_id']; ?>">
                                        <?php echo $t['task_label']; ?>
                                    </label>
                                </div>
                            </div>
                        <?php endwhile; else: ?>
                            <div class="col-12 text-center py-2">
                                <a href="init_tasks.php?id=<?php echo $id; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-4">ສ້າງລາຍການວຽກເລີ່ມຕົ້ນ</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-light p-3 rounded-4 border mt-3">
                        <div class="d-flex justify-content-between mb-1 small fw-bold">
                            <span>ຄວາມຄືບໜ້າການກຽມຕົວ:</span>
                            <span class="text-primary" id="progressText"><?php echo $percent; ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="progressBar" style="width: <?php echo $percent; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ເບື້ອງຂວາ (4/12) -->
            <div class="col-lg-4">
                <!-- ສະຫຼຸບການເງິນ -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-dark text-white">
                    <h6 class="fw-bold border-bottom border-secondary pb-2 mb-3 text-uppercase small">ສະຫຼຸບການເງິນ (Admin)</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="opacity-75">ຍອດຂາຍລວມ:</small>
                        <span class="fw-bold"><?php echo number_format($total_sale); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="opacity-75">ຕົ້ນທຶນລວມ:</small>
                        <span class="fw-bold text-info">- <?php echo number_format($total_cost); ?></span>
                    </div>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">ກຳໄລສຸດທິ:</h6>
                        <h4 class="mb-0 text-success fw-bold">+ <?php echo number_format($profit); ?></h4>
                    </div>
                </div>

                <!-- ພາຫະນະ ແລະ ໄກ້ -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-bus-alt text-warning me-2"></i>ພາຫະນະ ແລະ ໄກ້</h6>
                    <div class="mb-3">
                        <small class="text-muted d-block small">ລົດທົວ:</small>
                        <p class="fw-bold mb-0"><?php echo $row['car_model'] ?: 'ຍັງບໍ່ໄດ້ກຳນົດ'; ?></p>
                        <span class="badge bg-light text-dark border small"><?php echo $row['plate_number']; ?></span>
                    </div>
                    <div>
                        <small class="text-muted d-block small">ໄກ້ຜູ້ນຳທ່ຽວ:</small>
                        <p class="fw-bold mb-0 text-primary"><?php echo $row['guide_name'] ?: 'ຍັງບໍ່ໄດ້ກຳນົດ'; ?></p>
                    </div>
                </div>

                <!-- ຫຼັກຖານການໂອນ -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white no-print">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-file-invoice-dollar text-success me-2"></i>ຫຼັກຖານການຊຳລະ</h6>
                    <?php if($row['payment_slip']): ?>
                        <div class="text-center">
                            <a href="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['payment_slip']; ?>" target="_blank">
                                <img src="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['payment_slip']; ?>" class="img-fluid rounded-3 shadow-sm border mb-2" style="max-height: 300px; width: 100%; object-fit: contain;">
                            </a>
                            <div class="text-start mt-3 p-2 bg-light rounded-3 small">
                                <p class="mb-1 small">ວິທີຈ່າຍ: <strong><?php echo $row['payment_method']; ?></strong></p>
                                <p class="mb-0 small">ວັນທີ: <strong><?php echo date('d/m/Y H:i', strtotime($row['payment_date'])); ?></strong></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted small italic">ຍັງບໍ່ມີຫຼັກຖານການໂອນ</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Logic Toggle Task (AJAX)
document.querySelectorAll('.task-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const taskId = this.dataset.id;
        const bookingId = this.dataset.bid;
        const status = this.checked ? 1 : 0;
        const label = document.getElementById('label-' + taskId);
        fetch(`toggle_task.php?task_id=${taskId}&status=${status}&booking_id=${bookingId}`)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    if(status === 1) { label.classList.add('text-muted', 'text-decoration-line-through'); label.classList.remove('fw-bold'); }
                    else { label.classList.remove('text-muted', 'text-decoration-line-through'); label.classList.add('fw-bold'); }
                    updateProgressBar();
                }
            });
    });
});
function updateProgressBar() {
    const total = document.querySelectorAll('.task-checkbox').length;
    const checked = document.querySelectorAll('.task-checkbox:checked').length;
    const percent = total > 0 ? Math.round((checked / total) * 100) : 0;
    document.getElementById('progressBar').style.width = percent + '%';
    document.getElementById('progressText').innerText = percent + '%';
}
updateProgressBar();
</script>

<style>
    .main-content { background-color: #f4f6f9; }
    .card { border: 1px solid rgba(0,0,0,0.05) !important; }
    .task-checkbox { width: 1.2rem; height: 1.2rem; cursor: pointer; }
    @media print { .sidebar, .no-print, nav { display: none !important; } .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; } }
</style>
<?php include '../../includes/footer.php'; ?>