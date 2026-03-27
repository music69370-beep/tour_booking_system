<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content">
    <!-- Navbar ຈະຕິດຢູ່ເທິງສຸດ (Sticky) ຍ້ອນ class ໃນ navbar.php -->
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5 mt-3">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark font-lao"><i class="fas fa-plus-circle text-primary me-2"></i>ເພີ່ມແພັກເກັດທົວໃໝ່</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm font-lao">ຍົກເລີກ</a>
        </div>

        <form action="save.php" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                
                <!-- 1. Logistics & Management -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4 text-primary font-lao"><i class="fas fa-tasks me-2"></i>1. ຂໍ້ມູນດ້ານການບໍລິຫານ (Logistics)</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small font-lao">ລະຫັດແພັກເກັດ (Tour Code)</label>
                                <input type="text" name="tour_code" class="form-control bg-light border-0 font-lao" placeholder="PKG-VTE-001" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small font-lao">ຊື່ແພັກເກັດທົວ</label>
                                <input type="text" name="tour_name" class="form-control bg-light border-0 font-lao" placeholder="ຕົວຢ່າງ: ທ່ຽວຫຼວງພະບາງ 3 ມື້ 2 ຄືນ" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small font-lao">ໝວດໝູ່ທົວ</label>
                                <select name="category" class="form-select bg-light border-0 font-lao">
                                    <option value="ທົວວັດທະນະທຳ">ທົວວັດທະນະທຳ</option>
                                    <option value="ທົວຜະຈົນໄພ">ທົວຜະຈົນໄພ</option>
                                    <option value="ທົວຄອບຄົວ">ທົວຄອບຄົວ</option>
                                    <option value="ທົວພັກຜ່ອນ">ທົວພັກຜ່ອນ</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-danger font-lao">ລາຄາຂາຍ/ຄົນ</label>
                                <input type="number" name="price" class="form-control border-danger font-lao" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small font-lao">ຕົ້ນທຶນ/ຄົນ</label>
                                <input type="number" name="cost_per_person" class="form-control bg-light border-0 font-lao" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small font-lao">ຂັ້ນຕ່ຳ (Min Pax)</label>
                                <input type="number" name="min_pax" class="form-control bg-light border-0 font-lao" value="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-primary font-lao">ບ່ອນນັ່ງທັງໝົດ (Max Seats)</label>
                                <input type="number" name="max_seats" id="max_seats" class="form-control border-primary font-lao" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small font-lao">ສະຖານທີ່ນັດພົບ (Meeting Point)</label>
                                <input type="text" name="meeting_point" class="form-control bg-light border-0 font-lao" placeholder="ບອກຈຸດນັດພົບ ຫຼື ບ່ອນຂຶ້ນລົດ...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small font-lao">ເລືອກລົດທົວ</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select bg-light border-0 font-lao" onchange="updateMaxSeats()" required>
                                    <option value="">-- ເລືອກລົດ --</option>
                                    <?php 
                                    $res_v = mysqli_query($conn, "SELECT * FROM vehicles WHERE status = 'Available'");
                                    while($v = mysqli_fetch_assoc($res_v)) {
                                        echo "<option value='".$v['vehicle_id']."' data-cap='".$v['capacity']."'>".$v['model']." (".$v['plate_number'].")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small font-lao">ເລືອກໄກ້ຜູ້ນຳທ່ຽວ</label>
                                <select name="guide_id" class="form-select bg-light border-0 font-lao" required>
                                    <option value="">-- ເລືອກໄກ້ --</option>
                                    <?php 
                                    $res_g = mysqli_query($conn, "SELECT * FROM guides WHERE status = 'Available'");
                                    while($g = mysqli_fetch_assoc($res_g)) echo "<option value='".$g['guide_id']."'>".$g['fullname']."</option>";
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ສິ່ງທີ່ລູກຄ້າຈະໄດ້ຮັບ -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-success font-lao"><i class="fas fa-check-double me-2"></i>2. ສິ່ງທີ່ລູກຄ້າຈະໄດ້ຮັບ</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold small font-lao">ສິ່ງທີ່ລວມຢູ່ນຳ (What's Included)</label>
                            <textarea name="whats_included" class="form-control bg-light border-0 font-lao" rows="3" placeholder="- ຄ່າປີ້ເຂົ້າຊົມ&#10;- ປະກັນໄພ..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small font-lao">ສິ່ງທີ່ບໍ່ລວມ (What's Excluded)</label>
                            <textarea name="whats_excluded" class="form-control bg-light border-0 font-lao" rows="3" placeholder="- ຄ່າຕິບໄກ້..."></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-danger font-lao">ນະໂຍບາຍການຍົກເລີກ</label>
                            <textarea name="cancellation_policy" class="form-control bg-light border-0 font-lao" rows="2" placeholder="ຍົກເລີກກ່ອນ 3 ວັນ ໄດ້ເງິນຄືນ 100%..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- 3. ການຕະຫຼາດ ແລະ ຈຸດເດັ່ນ -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-warning font-lao"><i class="fas fa-bullhorn me-2"></i>3. ການຕະຫຼາດ ແລະ ຈຸດເດັ່ນ</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold small font-lao">ຈຸດເດັ່ນຂອງທົວ (Tour Highlights)</label>
                            <textarea name="highlights" class="form-control bg-light border-0 font-lao" rows="3" placeholder="* ຊົມຕາເວັນຕົກດິນທີ່ພູສີ..."></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small font-lao">ຮູບໜ້າປົກ</label>
                                <input type="file" name="image" class="form-control bg-light border-0 small font-lao" accept="image/*" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small font-lao">ຮູບອື່ນໆ (Gallery)</label>
                                <input type="file" name="gallery[]" class="form-control bg-light border-0 small font-lao" accept="image/*" multiple>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small font-lao">ສະຖານະແພັກເກັດ</label>
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox" name="status" value="Active" id="statusSwitch" checked>
                                    <label class="form-check-label fw-bold font-lao" for="statusSwitch">ເປີດການຈອງ (Active)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Details -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4 text-info font-lao"><i class="fas fa-map-signs me-2"></i>ແຜນການເດີນທາງ ແລະ ກິດຈະກຳ</h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small font-lao">ລາຍລະອຽດແຜນການເດີນທາງ (Itinerary)</label>
                                <textarea name="itinerary" class="form-control bg-light border-0 font-lao" rows="6"></textarea>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small font-lao">ໄລຍະເວລາ</label>
                                    <input type="text" name="duration" class="form-control bg-light border-0 font-lao" placeholder="3 ມື້ 2 ຄືນ">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small font-lao">ຈຳນວນຄາບອາຫານ</label>
                                    <input type="number" name="meals" class="form-control bg-light border-0 font-lao" value="0">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-bold small font-lao">ກິດຈະກຳຫຼັກ</label>
                                    <textarea name="activities" class="form-control bg-light border-0 font-lao" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-5 mb-5">
                    <button type="submit" name="save_tour" class="btn btn-primary btn-lg px-5 rounded-pill shadow font-lao">
                        <i class="fas fa-save me-2"></i> ບັນທຶກແພັກເກັດທົວ
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
function updateMaxSeats() {
    const select = document.getElementById('vehicle_id');
    const seatsInput = document.getElementById('max_seats');
    const selectedOption = select.options[select.selectedIndex];
    const capacity = selectedOption.getAttribute('data-cap');
    if (capacity) seatsInput.value = capacity;
}
</script>

<?php include '../../includes/footer.php'; ?>