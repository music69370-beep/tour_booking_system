<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-folder-plus text-primary me-2"></i>ເພີ່ມແພັກເກັດທົວໃໝ່</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍົກເລີກ</a>
        </div>

        <form action="save.php" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                
                <!-- 1. Logistics & Marketing -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-info-circle me-2"></i>1. ຂໍ້ມູນແພັກເກັດ ແລະ ກຳນົດວັນທີ</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">ລະຫັດແພັກເກັດ</label>
                                <input type="text" name="tour_code" class="form-control bg-light border-0" placeholder="PKG-001" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຊື່ແພັກເກັດທົວ</label>
                                <input type="text" name="tour_name" class="form-control bg-light border-0" placeholder="ຊື່ແພັກເກັດ..." required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">ໝວດໝູ່</label>
                                <select name="category" class="form-select bg-light border-0">
                                    <option value="ທົວວັດທະນະທຳ">ທົວວັດທະນະທຳ</option>
                                    <option value="ທົວຜະຈົນໄພ">ທົວຜະຈົນໄພ</option>
                                    <option value="ທົວຄອບຄົວ">ທົວຄອບຄົວ</option>
                                    <option value="ທົວພັກຜ່ອນ">ທົວພັກຜ່ອນ</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-danger">ວັນທີເລີ່ມເດີນທາງ</label>
                                <input type="date" name="start_date" class="form-control border-danger" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-danger">ວັນທີສິ້ນສຸດ</label>
                                <input type="date" name="end_date" class="form-control border-danger" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-primary">ລາຄາຂາຍ/ທ່ານ</label>
                                <input type="number" name="price" class="form-control border-primary" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">ຕົ້ນທຶນ/ທ່ານ (Cost)</label>
                                <input type="number" name="cost_per_person" class="form-control bg-light border-0" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Resources & Seats -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-info"><i class="fas fa-bus me-2"></i>2. ການຈັດສັນລົດ ແລະ ໄກ້</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ເລືອກລົດທົວ</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select bg-light border-0" onchange="updateMaxSeats()" required>
                                    <option value="" data-cap="0">-- ເລືອກລົດ --</option>
                                    <?php 
                                    $v_res = mysqli_query($conn, "SELECT * FROM vehicles WHERE status='Available'");
                                    while($v = mysqli_fetch_assoc($v_res)) echo "<option value='".$v['vehicle_id']."' data-cap='".$v['capacity']."'>".$v['model']." (".$v['plate_number'].")</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ເລືອກໄກ້ຜູ້ນຳທ່ຽວ</label>
                                <select name="guide_id" class="form-select bg-light border-0" required>
                                    <option value="">-- ເລືອກໄກ້ --</option>
                                    <?php 
                                    $g_res = mysqli_query($conn, "SELECT * FROM guides WHERE status='Available'");
                                    while($g = mysqli_fetch_assoc($g_res)) echo "<option value='".$g['guide_id']."'>".$g['fullname']."</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">ບ່ອນນັ່ງສູງສຸດ</label>
                                <input type="number" name="max_seats" id="max_seats" class="form-control border-primary fw-bold" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">ຈຳນວນຄົນຂັ້ນຕ່ຳ</label>
                                <input type="number" name="min_pax" class="form-control bg-light border-0" value="1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">ອາຫານ (ຄາບ)</label>
                                <input type="number" name="meals" class="form-control bg-light border-0" value="0">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ສະຖານທີ່ນັດພົບ (Meeting Point)</label>
                                <input type="text" name="meeting_point" class="form-control bg-light border-0" placeholder="ບອກບ່ອນຂຶ້ນລົດ...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Images -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-warning"><i class="fas fa-image me-2"></i>3. ຮູບພາບປະກອບ</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">ຮູບໜ້າປົກ (Cover)</label>
                            <input type="file" name="image" class="form-control bg-light border-0 small" accept="image/*" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small">ຮູບ Gallery (ເລືອກໄດ້ຫຼາຍຮູບ)</label>
                            <input type="file" name="gallery[]" class="form-control bg-light border-0 small" accept="image/*" multiple>
                        </div>
                    </div>
                </div>

                <!-- 4. Details -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-list me-2"></i>4. ລາຍລະອຽດສິ່ງທີ່ລວມ ແລະ ແຜນການ</h5>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label small fw-bold">ຈຸດເດັ່ນ (Highlights)</label><textarea name="highlights" class="form-control bg-light border-0" rows="3"></textarea></div>
                            <div class="col-md-4"><label class="form-label small fw-bold">ສິ່ງທີ່ລວມ</label><textarea name="whats_included" class="form-control bg-light border-0" rows="3"></textarea></div>
                            <div class="col-md-4"><label class="form-label small fw-bold">ສິ່ງທີ່ບໍ່ລວມ</label><textarea name="whats_excluded" class="form-control bg-light border-0" rows="3"></textarea></div>
                            <div class="col-md-8"><label class="form-label small fw-bold">ແຜນການເດີນທາງ (Itinerary)</label><textarea name="itinerary" class="form-control bg-light border-0" rows="5"></textarea></div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">ນະໂຍບາຍການຍົກເລີກ</label><textarea name="cancellation_policy" class="form-control bg-light border-0" rows="2"></textarea>
                                <label class="form-label small fw-bold mt-2">ກິດຈະກຳຫຼັກ</label><textarea name="activities" class="form-control bg-light border-0" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-5 mb-5">
                    <button type="submit" name="save_tour" class="btn btn-primary btn-lg px-5 rounded-pill shadow fw-bold">ບັນທຶກແພັກເກັດທົວ</button>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
function updateMaxSeats() {
    const select = document.getElementById('vehicle_id');
    const cap = select.options[select.selectedIndex].getAttribute('data-cap');
    document.getElementById('max_seats').value = cap || 0;
}
</script>
<?php include '../../includes/footer.php'; ?>