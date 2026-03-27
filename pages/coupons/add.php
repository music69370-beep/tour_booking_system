<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-ticket-alt text-danger me-2"></i>ສ້າງຄູປອງສ່ວນຫຼຸດໃໝ່ (ແບບລະອຽດ)</h2>
        </div>

        <form action="save.php" method="POST">
            <div class="row g-4">
                
                <!-- ສ່ວນທີ່ 1: ຂໍ້ມູນຫຼັກ ແລະ ປະເພດສ່ວນຫຼຸດ -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-percentage me-2"></i>1. ຂໍ້ມູນສ່ວນຫຼຸດ (Discount Type)</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ລະຫັດຄູປອງ (Promo Code)</label>
                                <input type="text" name="code" class="form-control bg-light border-0 py-2 fw-bold text-primary" style="text-transform: uppercase;" placeholder="ຕົວຢ່າງ: BEEP2024" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ປະເພດສ່ວນຫຼຸດ</label>
                                <select name="discount_type" id="discount_type" class="form-select bg-light border-0" onchange="toggleMaxDiscount()">
                                    <option value="Fixed">ຫຼຸດເປັນຈຳນວນເງິນ (ກີບ)</option>
                                    <option value="Percent">ຫຼຸດເປັນເປີເຊັນ (%)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ມູນຄ່າສ່ວນຫຼຸດ</label>
                                <input type="number" name="discount_value" class="form-control bg-light border-0" placeholder="0" required>
                            </div>
                            <div class="col-md-12" id="max_discount_div" style="display:none;">
                                <label class="form-label fw-bold small text-danger">ເພພານສ່ວນຫຼຸດສູງສຸດ (Max Discount Cap)</label>
                                <input type="number" name="max_discount" class="form-control border-danger" value="0">
                                <small class="text-muted">ກໍລະນີຫຼຸດເປັນ %, ຈະຫຼຸດບໍ່ເກີນຍອດເງິນນີ້</small>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ວັນໝົດອາຍຸ</label>
                                <input type="date" name="expiry_date" class="form-control bg-light border-0" value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ສ່ວນທີ່ 2: ເງື່ອນໄຂ ແລະ ການຈຳກັດສິດ -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-success"><i class="fas fa-user-shield me-2"></i>2. ເງື່ອນໄຂການນຳໃຊ້</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ຍອດຊື້ຂັ້ນຕ່ຳ (Minimum Spend)</label>
                                <input type="number" name="min_spend" class="form-control bg-light border-0" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຈຳນວນສິດທັງໝົດ</label>
                                <input type="number" name="total_limit" class="form-control bg-light border-0" value="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຈຳນວນສິດ/ຄົນ</label>
                                <input type="number" name="limit_per_user" class="form-control bg-light border-0" value="1">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ໃຊ້ໄດ້ກັບແພັກເກັດ:</label>
                                <select name="specific_tour_id" class="form-select bg-light border-0">
                                    <option value="">ທຸກແພັກເກັດທົວ (All Tours)</option>
                                    <?php 
                                    $tours = mysqli_query($conn, "SELECT tour_id, tour_name FROM tours WHERE status='Active'");
                                    while($t = mysqli_fetch_assoc($tours)) echo "<option value='".$t['tour_id']."'>".$t['tour_name']."</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="Active" id="activeSw" checked>
                                    <label class="form-check-label fw-bold" for="activeSw">ເປີດໃຊ້ງານຄູປອງນີ້ (Status Active)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-5 mb-5">
                    <button type="submit" name="save_coupon" class="btn btn-danger btn-lg px-5 rounded-pill shadow">
                        <i class="fas fa-save me-2"></i> ບັນທຶກຄູປອງໃໝ່
                    </button>
                    <a href="index.php" class="btn btn-light border btn-lg px-5 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
function toggleMaxDiscount() {
    const type = document.getElementById('discount_type').value;
    const div = document.getElementById('max_discount_div');
    // ຖ້າເລືອກເປັນ Percent (%) ໃຫ້ໂຊຊ່ອງ "ຫຼຸດສູງສຸດບໍ່ເກີນ..."
    div.style.display = (type === 'Percent') ? 'block' : 'none';
}
</script>

<?php include '../../includes/footer.php'; ?>