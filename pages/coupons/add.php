<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-ticket-alt text-danger me-2"></i>ສ້າງຄູປອງສ່ວນຫຼຸດໃໝ່</h2>
        </div>

        <form action="save.php" method="POST">
            <div class="row g-4">
                
                <!-- ສ່ວນທີ 1: ຂໍ້ມູນສ່ວນຫຼຸດ -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-percentage me-2"></i>1. ຕັ້ງຄ່າສ່ວນຫຼຸດ</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ລະຫັດຄູປອງ (Promo Code)</label>
                                <input type="text" name="code" class="form-control bg-light border-0 py-2 fw-bold text-primary" style="text-transform: uppercase;" placeholder="ຕົວຢ່າງ: DISCOUNT2024" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ປະເພດສ່ວນຫຼຸດ</label>
                                <select name="discount_type" id="discount_type" class="form-select bg-light border-0" onchange="handleTypeChange()">
                                    <option value="Fixed">ຫຼຸດເປັນຈຳນວນເງິນ (ກີບ)</option>
                                    <option value="Percent">ຫຼຸດເປັນເປີເຊັນ (%)</option>
                                </select>
                            </div>

                            <!-- ບ໋ອກນີ້ຈະປ່ຽນ Label ແລະ Placeholder ອັດຕະໂນມັດ -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-primary" id="value_label">ມູນຄ່າສ່ວນຫຼຸດ (ກີບ)</label>
                                <input type="number" name="discount_value" id="discount_value" class="form-control bg-light border-0 py-2 fw-bold" placeholder="0" required>
                            </div>

                            <!-- ບ໋ອກເພພານສ່ວນຫຼຸດ: ຈະໂຊສະເພາະຕອນເລືອກ Percent (%) -->
                            <div class="col-md-12" id="max_discount_div" style="display:none;">
                                <label class="form-label fw-bold small text-danger">ເພພານສ່ວນຫຼຸດສູງສຸດ (Max Discount Cap)</label>
                                <div class="input-group">
                                    <input type="number" name="max_discount" class="form-control border-danger bg-white" value="0">
                                    <span class="input-group-text border-danger bg-danger text-white">ກີບ</span>
                                </div>
                                <small class="text-muted">ລະບຸຍອດເງິນສູງສຸດທີ່ຈະຫຼຸດໃຫ້ (ຕົວຢ່າງ: ຫຼຸດ 10% ແຕ່ບໍ່ເກີນ 50,000 ກີບ)</small>
                            </div>

                            <div class="col-md-12 border-top pt-3">
                                <label class="form-label fw-bold small">ວັນໝົດອາຍຸຄູປອງ</label>
                                <input type="date" name="expiry_date" class="form-control bg-light border-0 py-2" value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ສ່ວນທີ 2: ເງື່ອນໄຂການນຳໃຊ້ -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-success"><i class="fas fa-user-shield me-2"></i>2. ເງື່ອນໄຂການນຳໃຊ້</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ຍອດຊື້ຂັ້ນຕ່ຳ (Minimum Spend)</label>
                                <input type="number" name="min_spend" class="form-control bg-light border-0" value="0">
                                <small class="text-muted">ຕ້ອງຈອງຢ່າງໜ້ອຍເທົ່າໃດ ຈຶ່ງຈະໃຊ້ໂຄ້ດນີ້ໄດ້</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຈຳນວນສິດທັງໝົດ</label>
                                <input type="number" name="total_limit" class="form-control bg-light border-0" value="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ສິດການໃຊ້/ຄົນ</label>
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
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-5 mb-5">
                    <button type="submit" name="save_coupon" class="btn btn-danger btn-lg px-5 rounded-pill shadow">
                        <i class="fas fa-save me-2"></i> ບັນທຶກຄູປອງ
                    </button>
                    <a href="index.php" class="btn btn-light btn-lg border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
function handleTypeChange() {
    const type = document.getElementById('discount_type').value;
    const valueLabel = document.getElementById('value_label');
    const discountValue = document.getElementById('discount_value');
    const maxDiv = document.getElementById('max_discount_div');

    if (type === 'Percent') {
        // ເມື່ອເລືອກເປັນ ເປີເຊັນ (%)
        valueLabel.innerHTML = 'ລະບຸເປີເຊັນສ່ວນຫຼຸດ (%)';
        valueLabel.classList.replace('text-primary', 'text-success');
        discountValue.placeholder = 'ຕົວຢ່າງ: 10';
        maxDiv.style.display = 'block'; // ໂຊບ໋ອກເພພານ
    } else {
        // ເມື່ອເລືອກເປັນ ຈຳນວນເງິນ (Fixed)
        valueLabel.innerHTML = 'ມູນຄ່າສ່ວນຫຼຸດ (ກີບ)';
        valueLabel.classList.replace('text-success', 'text-primary');
        discountValue.placeholder = 'ຕົວຢ່າງ: 50000';
        maxDiv.style.display = 'none'; // ຊ່ອນບ໋ອກເພພານ
    }
}

// ເອີ້ນໃຊ້ທັນທີທີ່ໂຫລດໜ້າ ເພື່ອເຊັກຄ່າເລີ່ມຕົ້ນ
handleTypeChange();
</script>

<?php include '../../includes/footer.php'; ?>