<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>ເພີ່ມແພັກເກັດທົວໃໝ່</h2>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <form action="save.php" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ຊື່ແພັກເກັດທົວ</label>
                        <input type="text" name="tour_name" class="form-control bg-light border-0 shadow-none" placeholder="ຕົວຢ່າງ: ທ່ຽວຫຼວງພະບາງ" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ເລືອກພາຫະນະ (ລົດທົວ)</label>
                        <select name="vehicle_id" id="vehicle_id" class="form-select bg-light border-0 shadow-none" onchange="updateMaxSeats()" required>
                            <option value="">-- ກະລຸນາເລືອກລົດ --</option>
                            <?php 
                            $res_v = mysqli_query($conn, "SELECT * FROM vehicles WHERE status = 'Available'");
                            while($v = mysqli_fetch_assoc($res_v)) {
                                echo "<option value='".$v['vehicle_id']."' data-cap='".$v['capacity']."'>".$v['model']." (".$v['plate_number'].")</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!-- ຊອກຫາບ່ອນເລືອກລົດ (vehicle_id) ແລ້ວເພີ່ມ Code ນີ້ລຸ່ມມັນ -->
                    <!-- ຊອກຫາສ່ວນເລືອກໄກ້ ໃນ add.php ແລ້ວວາງໂຕນີ້ໃສ່ໃຫ້ເປະ -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ເລືອກໄກ້ຜູ້ນຳທ່ຽວ</label>
                        <select name="guide_id" class="form-select bg-light border-0 shadow-none" required>
                            <option value="">-- ກະລຸນາເລືອກໄກ້ --</option>
                            <?php 
                            $res_g = mysqli_query($conn, "SELECT * FROM guides"); // ລອງດຶງທຸກຄົນມາກ່ອນ
                            while($g = mysqli_fetch_assoc($res_g)) {
                                echo "<option value='".$g['guide_id']."'>".$g['fullname']."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small">ລາຄາ (ກີບ)</label>
                        <input type="number" name="price" class="form-control bg-light border-0 shadow-none" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">ໄລຍະເວລາ</label>
                        <input type="text" name="duration" class="form-control bg-light border-0 shadow-none" placeholder="3 ມື້ 2 ຄືນ">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">ຈຳນວນຄາບອາຫານ</label>
                        <input type="number" name="meals" class="form-control bg-light border-0 shadow-none" value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-primary">ບ່ອນນັ່ງທັງໝົດ</label>
                        <input type="number" name="max_seats" id="max_seats" class="form-control bg-white border-primary fw-bold" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-primary">ຮູບໜ້າປົກ (Cover Image)</label>
                        <input type="file" name="image" class="form-control bg-light border-0 shadow-none" accept="image/*" required>
                    </div>

                    <!-- *** ເພີ່ມຊ່ອງອັບໂຫລດ Gallery *** -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-success">ຮູບພາບອື່ນໆປະກອບ (Gallery - ເລືອກໄດ້ຫຼາຍຮູບ)</label>
                        <input type="file" name="gallery[]" class="form-control bg-light border-0 shadow-none" accept="image/*" multiple>
                        <small class="text-muted small">* ກົດ Ctrl ຄ້າງໄວ້ເພື່ອເລືອກຫຼາຍຮູບ</small>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-danger">ລາຍລະອຽດແຜນການເດີນທາງ</label>
                        <textarea name="itinerary" class="form-control bg-light border-0 shadow-none" rows="4" placeholder="ມື້ທີ 1: ...&#10;ມື້ທີ 2: ..."></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-success">ກິດຈະກຳຫຼັກ</label>
                        <textarea name="activities" class="form-control bg-light border-0 shadow-none" rows="2" placeholder="ລວມກິດຈະກຳຫຍັງແດ່..."></textarea>
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top text-end">
                        <button type="submit" name="save_tour" class="btn btn-primary px-5 rounded-pill shadow">
                            <i class="fas fa-save me-2"></i> ບັນທຶກຂໍ້ມູນທົວ
                        </button>
                        <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function updateMaxSeats() {
    const select = document.getElementById('vehicle_id');
    const seatsInput = document.getElementById('max_seats');
    const selectedOption = select.options[select.selectedIndex];
    const capacity = selectedOption.getAttribute('data-cap');
    if (capacity) seatsInput.value = capacity; else seatsInput.value = 0;
}
</script>

<?php include '../../includes/footer.php'; ?>