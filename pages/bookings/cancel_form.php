<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$id = mysqli_real_escape_string($conn, $_GET['id']);
$sql = "SELECT b.*, c.fullname, t.tour_name, 
        (SELECT SUM(amount) FROM payments WHERE booking_id = b.booking_id) as paid_total
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        WHERE b.booking_id = '$id'";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-danger"><i class="fas fa-times-circle me-2"></i>ດຳເນີນການຍົກເລີກການຈອງ #BK-<?php echo $id; ?></h2>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">ຂໍ້ມູນການຈອງ</h5>
                    <p>ລູກຄ້າ: <strong><?php echo $row['fullname']; ?></strong></p>
                    <p>ທົວ: <strong><?php echo $row['tour_name']; ?></strong></p>
                    <p>ຍອດເງິນທີ່ຈ່າຍແລ້ວ: <strong class="text-success"><?php echo number_format($row['paid_total'] ?? 0); ?> ກີບ</strong></p>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <form action="cancel_process.php" method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo $id; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">ຈຳນວນເງິນທີ່ຄືນໃຫ້ລູກຄ້າ (Refund Amount)</label>
                            <input type="number" name="refund_amount" class="form-control bg-light border-0" value="0" required>
                            <small class="text-muted small">ເງິນທີ່ໂອນຄືນໃຫ້ລູກຄ້າແທ້ໆ</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">ຕົ້ນທຶນທີ່ເສຍໄປ (Cancellation Cost)</label>
                            <input type="number" name="cancellation_cost" class="form-control bg-light border-0" value="0" required>
                            <small class="text-muted small">ຄ່າທຳນຽມ ຫຼື ເງິນທີ່ບໍລິສັດຈ່າຍໄປແລ້ວຄືນບໍ່ໄດ້ (ເຊັ່ນ: ຈ່າຍໂຮງແຮມໄປແລ້ວ)</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">ເຫດຜົນການຍົກເລີກ</label>
                            <textarea name="cancel_reason" class="form-control bg-light border-0" rows="3" placeholder="ລະບຸເຫດຜົນ..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="confirm_cancel" class="btn btn-danger btn-lg rounded-pill shadow">ຢືນຢັນການຍົກເລີກ</button>
                            <a href="index.php" class="btn btn-light border rounded-pill">ກັບຄືນ</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="alert alert-info border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold"><i class="fas fa-info-circle me-2"></i>ການຄິດໄລ່ກຳໄລ</h5>
                    <p class="small">ເມື່ອທ່ານຍົກເລີກ, ກຳໄລຈະຖືກຄິດໄລ່ໃໝ່ດັ່ງນີ້:</p>
                    <div class="bg-white p-3 rounded-3 fw-bold small">
                        ກຳໄລ = (ເງິນທີ່ຮັບມາ - ເງິນທີ່ຄືນລູກຄ້າ) - (ຕົ້ນທຶນປົກກະຕິ + ຕົ້ນທຶນທີ່ເສຍໄປຈາກການຍົກເລີກ)
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>