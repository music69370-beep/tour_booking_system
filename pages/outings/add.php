<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-paper-plane text-info me-2"></i>ປ່ອຍລົດອອກທົວໃໝ່</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍ້ອນກັບ</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <form action="save.php" method="POST">
                <div class="row g-4">
                    <!-- ເລືອກແພັກເກັດທົວ -->
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">1. ເລືອກແພັກເກັດທົວ</label>
                        <select name="tour_id" id="tour_select" class="form-select bg-light border-0 py-2 shadow-none" onchange="syncTourDates()" required>
                            <option value="">-- ເລືອກທົວທີ່ກຳລັງຈະເດີນທາງ --</option>
                            <?php 
                            $t_res = mysqli_query($conn, "SELECT * FROM tours WHERE status='Active' AND start_date >= CURDATE() ORDER BY start_date ASC");
                            while($t = mysqli_fetch_assoc($t_res)) {
                                echo "<option value='{$t['tour_id']}' data-start='{$t['start_date']}' data-end='{$t['end_date']}'>{$t['tour_name']} (".date('d/m/Y', strtotime($t['start_date'])).")</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- ເລືອກລົດ -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">2. ເລືອກລົດ (ສະເພາະຄັນທີ່ວ່າງ)</label>
                        <select name="vehicle_id" class="form-select bg-light border-0 py-2 shadow-none" required>
                            <option value="">-- ເລືອກລົດ --</option>
                            <?php 
                            $v_res = mysqli_query($conn, "SELECT * FROM vehicles WHERE status='Available'");
                            while($v = mysqli_fetch_assoc($v_res)) {
                                echo "<option value='{$v['vehicle_id']}'>{$v['model']} - {$v['plate_number']} (ບ່ອນນັ່ງ: {$v['capacity']})</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- ເລືອກຄົນຂັບ -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">3. ເລືອກຄົນຂັບ (ສະເພາະຄົນທີ່ວ່າງ)</label>
                        <select name="driver_id" class="form-select bg-light border-0 py-2 shadow-none" required>
                            <option value="">-- ເລືອກຄົນຂັບ --</option>
                            <?php 
                            $d_res = mysqli_query($conn, "SELECT * FROM drivers WHERE status='Available'");
                            while($d = mysqli_fetch_assoc($d_res)) {
                                echo "<option value='{$d['driver_id']}'>{$d['fullname']} ({$d['phone']})</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small">ວັນທີອອກເດີນທາງ</label>
                        <input type="date" name="start_date" id="start_date" class="form-control bg-light border-0 shadow-none" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">ວັນທີກັບ</label>
                        <input type="date" name="return_date" id="return_date" class="form-control bg-light border-0 shadow-none" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-primary">ເລກໄມເລີ່ມຕົ້ນ (ກ່ອນອອກ)</label>
                        <input type="number" name="start_mileage" class="form-control border-primary shadow-none" placeholder="0" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold small">ໝາຍເຫດ</label>
                        <textarea name="notes" class="form-control bg-light border-0 shadow-none" rows="2" placeholder="ງົບນ້ຳມັນ ຫຼື ຂໍ້ມູນອື່ນໆ..."></textarea>
                    </div>

                    <div class="col-12 text-center pt-3 border-top">
                        <button type="submit" name="btn_save" class="btn btn-primary btn-lg px-5 rounded-pill shadow fw-bold">
                            <i class="fas fa-save me-1"></i> ຢືນຢັນການປ່ອຍລົດ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function syncTourDates() {
    const sel = document.getElementById('tour_select');
    const opt = sel.options[sel.selectedIndex];
    if(opt.value) {
        document.getElementById('start_date').value = opt.getAttribute('data-start');
        document.getElementById('return_date').value = opt.getAttribute('data-end');
    }
}
</script>
<?php include '../../includes/footer.php'; ?>