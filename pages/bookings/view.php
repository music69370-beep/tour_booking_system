<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. SQL ປັບປຸງໃໝ່: ເພີ່ມ LEFT JOIN users u ON b.created_by = u.user_id
$sql = "SELECT b.*, 
               c.fullname, c.phone, c.email, c.id_card_no, c.nationality, c.address as cust_address,
               c.emergency_name, c.emergency_phone,
               t.tour_name, t.tour_code, t.itinerary, t.image, t.highlights, t.duration, 
               t.whats_included, t.whats_excluded, t.cancellation_policy, t.meeting_point,
               u.fullname as staff_name, u.role as staff_role
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        LEFT JOIN users u ON b.created_by = u.user_id 
        WHERE b.booking_id = '$id'";

$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);

if (!$row) { echo "<div class='container mt-5'><div class='alert alert-danger font-lao'>ບໍ່ພົບຂໍ້ມູນການຈອງນີ້</div></div>"; exit; }

$all_seats = explode(',', $row['selected_seats']);
$tasks_res = mysqli_query($conn, "SELECT * FROM booking_tasks WHERE booking_id = '$id'");
?>

<style>
    .main-content { background-color: #f4f7f6; }
    .card-detail { border: none; border-radius: 25px; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .section-title { font-weight: 700; color: #2d3436; border-bottom: 2px solid #f1f3f7; padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; }
    .section-title i { width: 35px; height: 35px; background: #0d6efd; color: #fff; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 0.9rem; }
    .info-label { font-size: 0.8rem; color: #636e72; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 3px; }
    .info-value { font-size: 1rem; color: #2d3436; font-weight: 700; }
    .tour-img-lg { width: 100%; height: 250px; object-fit: cover; border-radius: 20px; }
    .timeline-day { border-left: 2px solid #0d6efd; padding-left: 20px; position: relative; margin-bottom: 20px; }
    .timeline-day::before { content: ''; position: absolute; left: -9px; top: 0; width: 16px; height: 16px; background: #0d6efd; border-radius: 50%; border: 3px solid #fff; }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 py-4">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">ລາຍລະອຽດການຈອງ #BK-<?php echo $id; ?></h2>
                <div class="mt-1">
                    <span class="text-muted small me-3"><i class="far fa-calendar-alt"></i> ວັນທີຈອງ: <?php echo date('d/m/Y H:i', strtotime($row['booking_date'])); ?></span>
                    <!-- ສ່ວນທີ່ເພີ່ມໃໝ່: ສະແດງຊື່ພະນັກງານ -->
                    <span class="text-primary small fw-bold">
                        <i class="fas fa-user-edit"></i> ຜູ້ບັນທຶກ: 
                        <?php echo $row['staff_name'] ? $row['staff_name'] . " (" . $row['staff_role'] . ")" : "ບໍ່ມີຂໍ້ມູນ (Online Booking)"; ?>
                    </span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍ້ອນກັບ</a>
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 shadow-sm"><i class="fas fa-print me-2"></i> ພິມ</button>
            </div>
        </div>

        <div class="row g-4">
            <!-- ຝັ່ງຊ້າຍ: ຂໍ້ມູນທົວ ແລະ ລູກຄ້າ -->
            <div class="col-lg-8">
                
                <!-- 1. ຂໍ້ມູນແພັກເກັດທົວ -->
                <div class="card card-detail p-4 mb-4">
                    <div class="row">
                        <div class="col-md-5">
                            <?php 
                                $t_img = $row['image'];
                                $display_t_img = (strpos($t_img, 'http') === 0) ? $t_img : "../../assets/uploads/tours/" . $t_img;
                            ?>
                            <img src="<?php echo $display_t_img; ?>" class="tour-img-lg shadow-sm">
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-primary rounded-pill px-3 py-2 mb-2"><?php echo $row['duration']; ?></span>
                                <span class="text-muted small">Code: <?php echo $row['tour_code']; ?></span>
                            </div>
                            <h3 class="fw-bold text-dark"><?php echo $row['tour_name']; ?></h3>
                            <p class="text-primary fw-bold mb-3"><i class="fas fa-map-marker-alt me-1"></i> ຈຸດນັດພົບ: <?php echo $row['meeting_point']; ?></p>
                            
                            <div class="bg-light p-3 rounded-4 border-start border-4 border-primary small">
                                <h6 class="fw-bold text-primary mb-1">ຈຸດເດັ່ນ (Highlights):</h6>
                                <?php echo nl2br($row['highlights']); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ຂໍ້ມູນຜູ້ຈອງ -->
                <div class="card card-detail p-4 mb-4">
                    <h5 class="section-title"><i class="fas fa-user-check"></i> ຂໍ້ມູນຜູ້ຈອງ ແລະ ຜູ້ຕິດຕໍ່</h5>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <span class="info-label">ຊື່ ແລະ ນາມສະກຸນ</span>
                            <div class="info-value text-primary"><?php echo $row['fullname']; ?></div>
                            <small class="badge bg-success-subtle text-success mt-1">ຫົວໜ້າຄະນະ (Lead)</small>
                        </div>
                        <div class="col-md-4">
                            <span class="info-label">ເບີໂທລະສັບ (WhatsApp)</span>
                            <div class="info-value"><?php echo $row['phone']; ?></div>
                        </div>
                        <div class="col-md-4">
                            <span class="info-label">ອີເມວ</span>
                            <div class="info-value"><?php echo $row['email']; ?></div>
                        </div>
                        <div class="col-md-4">
                            <span class="info-label">ສັນຊາດ / ບັດປະຈຳໂຕ</span>
                            <div class="info-value"><?php echo $row['nationality']; ?> (<?php echo $row['id_card_no']; ?>)</div>
                        </div>
                        <div class="col-md-8">
                            <span class="info-label">ທີ່ຢູ່</span>
                            <div class="info-value small"><?php echo $row['cust_address'] ?: 'ບໍ່ມີຂໍ້ມູນທີ່ຢູ່'; ?></div>
                        </div>
                    </div>
                </div>

                <!-- 3. ແຜນການເດີນທາງລະອຽດ -->
                <div class="card card-detail p-4 mb-4">
                    <h5 class="section-title"><i class="fas fa-route"></i> ແຜນການເດີນທາງ (Itinerary)</h5>
                    <?php 
                    $iti = json_decode($row['itinerary'], true);
                    if($iti): 
                        foreach($iti as $day): ?>
                        <div class="timeline-day">
                            <h6 class="fw-bold text-primary mb-3">ມື້ທີ <?php echo $day['day']; ?></h6>
                            <?php foreach($day['events'] as $ev): ?>
                                <div class="mb-2 small">
                                    <span class="fw-bold text-dark">● <?php echo $ev['location']; ?>:</span> 
                                    <span class="text-muted"><?php echo $ev['desc']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; 
                    else: echo "<p class='text-muted small'>ບໍ່ມີຂໍ້ມູນແຜນການເດີນທາງ</p>"; endif; ?>
                </div>
            </div>

            <!-- ຝັ່ງຂວາ -->
            <div class="col-lg-4">
                
                <div class="card card-detail p-4 bg-dark text-white text-center mb-4 shadow-lg">
                    <small class="opacity-75">ຍອດເງິນລວມທັງໝົດ</small>
                    <h2 class="price-text text-success my-2">₭ <?php echo number_format($row['total_price']); ?></h2>
                    <div class="d-flex justify-content-center gap-2 mt-2">
                        <span class="badge bg-primary px-3">Room: <?php echo $row['room_type']; ?></span>
                        <span class="badge bg-info px-3"><?php echo $row['num_people']; ?> ທ່ານ</span>
                    </div>
                </div>

                <!-- ສະຖານະການຈັດການວຽກ -->
                <div class="card card-detail p-4 mb-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-tasks text-primary me-2"></i> ລາຍການຈັດການວຽກ</h6>
                    <?php if(mysqli_num_rows($tasks_res) > 0): ?>
                        <?php while($ts = mysqli_fetch_assoc($tasks_res)): ?>
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom border-light">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" onchange="toggleTask(<?php echo $ts['task_id']; ?>)" <?php echo $ts['is_completed'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label small <?php echo $ts['is_completed'] ? 'text-decoration-line-through text-muted' : 'fw-bold'; ?>">
                                        <?php echo $ts['task_label']; ?>
                                    </label>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <a href="init_tasks.php?id=<?php echo $id; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">ສ້າງລາຍການວຽກອັດຕະໂນມັດ</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ສິ່ງທີ່ລວມ & ນະໂຍບາຍ -->
                <div class="card card-detail p-4 small">
                    <h6 class="fw-bold mb-3 text-success"><i class="fas fa-check-circle me-2"></i> ສິ່ງທີ່ລວມຢູ່ນຳ</h6>
                    <p class="text-muted mb-4"><?php echo nl2br($row['whats_included']); ?></p>
                    
                    <h6 class="fw-bold mb-3 text-danger"><i class="fas fa-exclamation-triangle me-2"></i> ນະໂຍບາຍການຍົກເລີກ</h6>
                    <p class="text-muted"><?php echo nl2br($row['cancellation_policy'] ?: 'ຕາມນະໂຍບາຍຂອງບໍລິສັດ'); ?></p>
                </div>

            </div>
        </div>
    </div>
</main>

<script>
function toggleTask(taskId) {
    fetch(`toggle_task.php?task_id=${taskId}`).then(() => {
        location.reload();
    });
}
</script>

<?php include '../../includes/footer.php'; ?>