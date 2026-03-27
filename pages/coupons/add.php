<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark font-lao"><i class="fas fa-plus-circle text-danger me-2"></i>ສ້າງຄູປອງສ່ວນຫຼຸດໃໝ່</h2>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4" style="max-width: 600px;">
            <form action="save.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold font-lao">ລະຫັດຄູປອງ (Promo Code)</label>
                    <input type="text" name="code" class="form-control bg-light border-0 py-2 fw-bold text-primary" style="text-transform: uppercase;" placeholder="ຕົວຢ່າງ: DISCOUNT10" required>
                    <div class="form-text">ລູກຄ້າຈະໃຊ້ລະຫັດນີ້ໃນໜ້າຈອງ</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold font-lao">ມູນຄ່າສ່ວນຫຼຸດ (ກີບ)</label>
                    <input type="number" name="discount_amount" class="form-control bg-light border-0 py-2" placeholder="0" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold font-lao">ວັນໝົດອາຍຸ</label>
                    <input type="date" name="expiry_date" class="form-control bg-light border-0 py-2" value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>" required>
                </div>
                
                <div class="pt-3">
                    <button type="submit" name="save_coupon" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow font-lao">ບັນທຶກຄູປອງ</button>
                    <a href="index.php" class="btn btn-link w-100 mt-2 text-muted text-decoration-none font-lao">ຍົກເລີກ</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>