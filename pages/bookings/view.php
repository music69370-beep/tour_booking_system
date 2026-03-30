<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

if (!isset($_GET['id'])) { echo "ID missing"; exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

// SQL ດຶງຂໍ້ມູນຄົບທຸກຢ່າງ
$sql = "SELECT b.*, c.fullname, c.phone, c.email, c.address, 
               t.tour_name, t.tour_code, t.duration, t.meeting_point, t.category, t.meals,
               t.price as price_per_pax, t.cost_per_person, t.highlights, t.whats_included, t.whats_excluded,
               v.model as car_model, v.plate_number,
               g.fullname as guide_name,
               p.payment_method, p.payment_slip, p.payment_date
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        LEFT JOIN vehicles v ON t.vehicle_id = v.vehicle_id
        LEFT JOIN guides g ON t.guide_id = g.guide_id
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        WHERE b.booking_id = '$id'";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) { echo "Data not found"; exit; }

// ຄຳນວນເງິນກຳໄລ (Admin)
$total_cost = $row['cost_per_person'] * $row['num_people'];
$profit = $row['total_price'] - $total_cost;

// ດຶງ Checklist
$tasks_query = mysqli_query($conn, "SELECT * FROM booking_tasks WHERE booking_id = '$id'");
$total_tasks = mysqli_num_rows($tasks_query);
$done_tasks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM booking_tasks WHERE booking_id = '$id' AND is_completed = 1"))['c'];
$percent = ($total_tasks > 0) ? round(($done_tasks / $total_tasks) * 100) : 0;
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-light border rounded-pill px-3 me-3 shadow-sm"><i class="fas fa-arrow-left"></i> ຍ້ອນກັບ</a>
                <h2 class="fw-bold mb-0">ລາຍລະອຽດການຈອງ #BK-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></h2>
            </div>
            <div class="d-flex gap-2">
                <?php if($row['status'] == 'Pending'): ?>
                    <button onclick="confirmApprove(<?php echo $id; ?>, 'approve.php')" class="btn btn-success rounded-pill px-4 shadow-sm">ອະນຸມັດ</button>
                <?php endif; ?>
                <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">ພິມໃບບິນ</button>
            </div>
        </div>

        <div class="row g-4">
            <!-- ເບື້ອງຊ້າຍ: ຂໍ້ມູນທົວ -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                    <div class="d-flex justify-content-between border-bottom pb-3 mb-4">
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2 px-3"><?php echo $row['category']; ?></span>
                            <h4 class="fw-bold text-dark mb-1"><?php echo $row['tour_name']; ?></h4>
                            <p class="text-muted small mb-0">Code: <strong><?php echo $row['tour_code']; ?></strong> | ວັນທີເດີນທາງ: <strong class="text-danger"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></strong></p>
                        </div>
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success px-4 py-2 shadow-sm h-100"><?php echo $row['status']; ?></span>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold text-primary mb-3">ຂໍ້ມູນຜູ້ຈອງ</h6>
                            <p class="mb-1">ຊື່: <strong><?php echo $row['fullname']; ?></strong></p>
                            <p class="mb-1">ເບີໂທ: <strong><?php echo $row['phone']; ?></strong></p>
                            <p class="small text-muted">ທີ່ຢູ່: <?php echo $row['address']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">ລາຍລະອຽດເດີນທາງ</h6>
                            <p class="small mb-1">ຈຸດນັດພົບ: <strong><?php echo $row['meeting_point']; ?></strong></p>
                            <p class="small">ຈຳນວນ: <span class="badge bg-dark text-white"><?php echo $row['num_people']; ?> ຄົນ</span></p>
                        </div>
                    </div>

                    <?php if($row['note']): ?>
                    <div class="mt-4 p-3 bg-light rounded-3 border-start border-warning border-4">
                        <small class="fw-bold text-warning d-block mb-1 text-uppercase">ໝາຍເຫດຈາກລູກຄ້າ:</small>
                        <p class="mb-0 small text-dark"><?php echo $row['note']; ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ລາຍຊື່ຜູ້ຮ່ວມທາງ -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">ລາຍຊື່ຜູ້ຮ່ວມເດີນທາງ</h6>
                    <table class="table table-sm">
                        <thead class="bg-light"><tr><th width="50">#</th><th>ຊື່</th><th>ເບີໂທ</th></tr></thead>
                        <tbody>
                            <tr><td>1</td><td><strong><?php echo $row['fullname']; ?></strong> (ຫົວໜ້າ)</td><td><?php echo $row['phone']; ?></td></tr>
                            <?php $parts = mysqli_query($conn, "SELECT * FROM booking_participants WHERE booking_id = '$id'"); $i=2; while($p = mysqli_fetch_assoc($parts)): ?>
                                <tr><td><?php echo $i++; ?></td><td><?php echo $p['participant_name']; ?></td><td><?php echo $p['participant_phone']; ?></td></tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ເບື້ອງຂວາ: ສະຫຼຸບ ແລະ Checklist -->
            <div class="col-lg-4">
                <!-- ການເງິນ -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-dark text-white">
                    <h6 class="fw-bold border-bottom border-secondary pb-2 mb-3 text-uppercase small">ສະຫຼຸບການເງິນ</h6>
                    <div class="d-flex justify-content-between mb-2"><small>ຍອດຂາຍລວມ:</small><span><?php echo number_format($row['total_price']); ?></span></div>
                    <?php if($row['discount_amount'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-warning"><small>ສ່ວນຫຼຸດຄູປອງ:</small><span>- <?php echo number_format($row['discount_amount']); ?></span></div>
                    <?php endif; ?>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between align-items-center"><h6>ກຳໄລສຸດທິ:</h6><h4 class="mb-0 text-success fw-bold">+ <?php echo number_format($profit); ?></h4></div>
                </div>

                <!-- ຫຼັກຖານການໂອນ -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white no-print">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">ຫຼັກຖານການຊຳລະ</h6>
                    <?php if($row['payment_slip']): ?>
                        <div class="text-center">
                            <a href="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['payment_slip']; ?>" target="_blank">
                                <img src="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['payment_slip']; ?>" class="img-fluid rounded shadow-sm border" style="max-height: 200px;">
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted small">ຍັງບໍ່ມີຫຼັກຖານ</div>
                    <?php endif; ?>
                </div>

                <!-- Checklist -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white no-print">
                    <h6 class="fw-bold text-dark mb-3">Checklist ຄວາມພ້ອມ</h6>
                    <div id="taskList">
                        <?php mysqli_data_seek($tasks_query, 0); while($t = mysqli_fetch_assoc($tasks_query)): $new_s = $t['is_completed'] ? 0 : 1; ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input task-checkbox shadow-none" type="checkbox" data-id="<?php echo $t['task_id']; ?>" data-bid="<?php echo $id; ?>" <?php echo $t['is_completed'] ? 'checked' : ''; ?>>
                                <label class="form-check-label ms-2 small <?php echo $t['is_completed'] ? 'text-muted text-decoration-line-through' : 'fw-bold'; ?>"><?php echo $t['task_label']; ?></label>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="bg-light p-3 rounded-4 border mt-3 text-center">
                        <div class="fw-bold text-primary mb-1"><?php echo $percent; ?>%</div>
                        <div class="progress" style="height: 6px;"><div class="progress-bar bg-success" style="width: <?php echo $percent; ?>%"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// AJAX Toggle Task (ຄືເກົ່າ)
document.querySelectorAll('.task-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const tid = this.dataset.id; const bid = this.dataset.bid; const status = this.checked ? 1 : 0;
        fetch(`toggle_task.php?task_id=${tid}&status=${status}&booking_id=${bid}`).then(res => res.json()).then(data => { if(data.success) location.reload(); });
    });
});
</script>
<?php include '../../includes/footer.php'; ?>