<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

if (!isset($_GET['id'])) { echo "ID missing"; exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. ດຶງຂໍ້ມູນການຈອງ (ແກ້ໄຂ SQL ໃຫ້ຕົງກັບ Schema ໃໝ່)
$sql = "SELECT b.*, c.fullname, c.phone, c.email, c.address, 
               t.tour_name, t.tour_code, t.duration, t.meeting_point, t.category, t.meals, t.image as tour_img,
               t.price as price_per_pax, t.highlights, t.whats_included, t.whats_excluded,
               p.amount as paid_amount, p.payment_method, p.payment_slip, p.payment_date,
               v.model as car_model, v.plate_number, d.fullname as driver_name
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        -- ເຊື່ອມຫາລົດ ແລະ ຄົນຂັບ ຜ່ານ Trip Log (vehicle_outings)
        LEFT JOIN vehicle_outings vo ON b.tour_id = vo.tour_id AND b.travel_date = vo.start_date
        LEFT JOIN vehicles v ON vo.vehicle_id = v.vehicle_id
        LEFT JOIN drivers d ON vo.driver_id = d.driver_id
        WHERE b.booking_id = '$id'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "SQL Error: " . mysqli_error($conn);
    exit;
}

$row = mysqli_fetch_assoc($result);

if (!$row) { echo "<div class='p-5 text-center'><h3>ບໍ່ພົບຂໍ້ມູນ</h3><a href='index.php'>ກັບຄືນ</a></div>"; exit; }

// 2. ດຶງຂໍ້ມູນ Checklist ຄວາມພ້ອມ
$tasks_query = mysqli_query($conn, "SELECT * FROM booking_tasks WHERE booking_id = '$id'");
$total_tasks = mysqli_num_rows($tasks_query);
$done_res = mysqli_query($conn, "SELECT COUNT(*) as c FROM booking_tasks WHERE booking_id = '$id' AND is_completed = 1");
$done_tasks = mysqli_fetch_assoc($done_res)['c'];
$percent = ($total_tasks > 0) ? round(($done_tasks / $total_tasks) * 100) : 0;
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print mt-3">
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-light border rounded-pill px-3 me-3 shadow-sm"><i class="fas fa-arrow-left"></i> ຍ້ອນກັບ</a>
                <h2 class="fw-bold mb-0">ລາຍລະອຽດ #BK-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></h2>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="fas fa-print"></i> ພິມ</button>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- ຂໍ້ມູນທົວ ແລະ ຜູ້ຈອງ -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-4">
                            <div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 mb-2"><?php echo $row['category']; ?></span>
                                <h3 class="fw-bold text-dark mb-1"><?php echo $row['tour_name']; ?></h3>
                                <p class="text-muted small mb-0">ວັນທີເດີນທາງ: <strong class="text-danger"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></strong></p>
                            </div>
                            <div class="badge rounded-pill px-3 py-2 h-50 <?php echo ($row['status']=='Confirmed')?'bg-success':'bg-warning text-dark'; ?> border shadow-sm">
                                <?php echo ($row['status']=='Confirmed')?'ຢືນຢັນແລ້ວ':'ລໍຖ້າອະນຸມັດ'; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h6 class="fw-bold text-primary mb-2 small text-uppercase">ຂໍ້ມູນຜູ້ຈອງ</h6>
                                <p class="mb-1">ຊື່: <strong><?php echo $row['fullname']; ?></strong></p>
                                <p class="mb-1">ເບີໂທ: <strong><?php echo $row['phone']; ?></strong></p>
                                <p class="small text-muted">ອີເມວ: <?php echo $row['email']; ?></p>
                            </div>
                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold text-primary mb-2 small text-uppercase">ລາຍລະອຽດເດີນທາງ</h6>
                                <p class="small mb-1">ຈຸດນັດພົບ: <strong><?php echo $row['meeting_point']; ?></strong></p>
                                <p class="small">ຈຳນວນ: <span class="badge bg-dark text-white px-2"><?php echo $row['num_people']; ?> ຄົນ</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ລາຍຊື່ຜູ້ຮ່ວມທາງ -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-users text-info me-2"></i>ລາຍຊື່ຜູ້ຮ່ວມເດີນທາງ</h6>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <table class="table table-sm align-middle">
                            <thead class="bg-light">
                                <tr><th class="ps-3" width="60">#</th><th>ຊື່ ແລະ ນາມສະກຸນ</th><th>ເບີໂທ</th></tr>
                            </thead>
                            <tbody>
                                <tr><td class="ps-3 text-muted">1</td><td><strong><?php echo $row['fullname']; ?></strong> (ຫົວໜ້າ)</td><td><?php echo $row['phone']; ?></td></tr>
                                <?php 
                                $participants = mysqli_query($conn, "SELECT * FROM booking_participants WHERE booking_id = '$id'");
                                $i = 2; while($p = mysqli_fetch_assoc($participants)): ?>
                                    <tr><td class="ps-3 text-muted"><?php echo $i++; ?></td><td><?php echo $p['participant_name']; ?></td><td><?php echo $p['participant_phone']; ?></td></tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Checklist -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-tasks text-danger me-2"></i>Checklist ຄວາມພ້ອມ</h6>
                    <div class="row" id="taskList">
                        <?php if($total_tasks > 0): 
                            mysqli_data_seek($tasks_query, 0); while($t = mysqli_fetch_assoc($tasks_query)): ?>
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input task-checkbox shadow-none" type="checkbox" data-id="<?php echo $t['task_id']; ?>" data-bid="<?php echo $id; ?>" <?php echo $t['is_completed'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label ms-2 small <?php echo $t['is_completed'] ? 'text-muted text-decoration-line-through' : 'fw-bold'; ?>"><?php echo $t['task_label']; ?></label>
                                </div>
                            </div>
                        <?php endwhile; else: ?>
                            <div class="col-12 text-center py-2"><a href="init_tasks.php?id=<?php echo $id; ?>" class="btn btn-sm btn-outline-primary rounded-pill">ສ້າງລາຍການວຽກ</a></div>
                        <?php endif; ?>
                    </div>
                    <div class="progress mt-3" style="height: 10px; border-radius: 10px;"><div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="progressBar" style="width: <?php echo $percent; ?>%"></div></div>
                    <div class="text-end small mt-1 fw-bold text-primary">ພ້ອມ: <span id="progressText"><?php echo $percent; ?>%</span></div>
                </div>
            </div>

            <!-- ເບື້ອງຂວາ (Summary) -->
            <div class="col-lg-4">
                <!-- ສະຫຼຸບຍອດເງິນ -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-dark text-white">
                    <h6 class="fw-bold border-bottom border-secondary pb-2 mb-3 text-uppercase small">ສະຫຼຸບການເງິນ</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="opacity-75">ລາຄາລວມ:</small>
                        <span class="fw-bold"><?php echo number_format($row['total_price'] + $row['discount_amount']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="opacity-75">ສ່ວນຫຼຸດ:</small>
                        <span class="fw-bold text-warning">- <?php echo number_format($row['discount_amount']); ?></span>
                    </div>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">ຍອດສຸດທິ:</h6>
                        <h3 class="mb-0 text-success fw-bold">₭ <?php echo number_format($row['total_price']); ?></h3>
                    </div>
                </div>

                <!-- ພາຫະນະ-ຄົນຂັບ (ດຶງມາຈາກ Trip Log) -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-bus-alt text-warning me-2"></i>ພາຫະນະ ແລະ ຄົນຂັບ</h6>
                    <?php if($row['car_model']): ?>
                        <div class="mb-3 small">ລົດ: <strong><?php echo $row['car_model']; ?></strong> (<?php echo $row['plate_number']; ?>)</div>
                        <div class="small">ຄົນຂັບ: <strong><?php echo $row['driver_name']; ?></strong></div>
                    <?php else: ?>
                        <div class="text-muted small italic">ຍັງບໍ່ໄດ້ມອບໝາຍລົດ (Dispatch)</div>
                        <a href="../outings/add.php" class="btn btn-sm btn-outline-primary mt-2 rounded-pill">ໄປມອບໝາຍລົດ</a>
                    <?php endif; ?>
                </div>

                <!-- ຫຼັກຖານການໂອນ -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white no-print">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-file-invoice-dollar text-success me-2"></i>ຫຼັກຖານການຊຳລະ</h6>
                    <?php if($row['payment_slip']): ?>
                        <div class="text-center">
                            <a href="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['payment_slip']; ?>" target="_blank">
                                <img src="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['payment_slip']; ?>" class="img-fluid rounded-3 shadow-sm border mb-2" style="max-height: 200px; width: 100%; object-fit: contain;">
                            </a>
                            <div class="text-start mt-3 small text-muted">ວິທີຈ່າຍ: <strong><?php echo $row['payment_method']; ?></strong></div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted small italic">ຍັງບໍ່ມີຫຼັກຖານ</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// AJAX Toggle Task
document.querySelectorAll('.task-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const tid = this.dataset.id; const bid = this.dataset.bid; const status = this.checked ? 1 : 0;
        fetch(`toggle_task.php?task_id=${tid}&status=${status}&booking_id=${bid}`).then(res => res.json()).then(data => {
            if(data.success) {
                const label = this.nextElementSibling;
                if(status === 1) { label.classList.add('text-muted', 'text-decoration-line-through'); label.classList.remove('fw-bold'); }
                else { label.classList.remove('text-muted', 'text-decoration-line-through'); label.classList.add('fw-bold'); }
                updateProgressUI();
            }
        });
    });
});

function updateProgressUI() {
    const total = document.querySelectorAll('.task-checkbox').length;
    const checked = document.querySelectorAll('.task-checkbox:checked').length;
    const perc = Math.round((checked / total) * 100);
    document.getElementById('progressBar').style.width = perc + '%';
    document.getElementById('progressText').innerText = perc + '%';
}
</script>

<style>
    .main-content { background-color: #f4f6f9; }
    .card { border: 1px solid rgba(0,0,0,0.05) !important; }
    @media print { .sidebar, .no-print, nav { display: none !important; } .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; } }
</style>

<?php include '../../includes/footer.php'; ?>