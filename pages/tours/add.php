<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <!-- ສ່ວນຫົວ -->
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark">
                <i class="fas fa-folder-plus text-primary me-2"></i>ເພີ່ມແພັກເກັດທົວໃໝ່
            </h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍົກເລີກ</a>
        </div>

        <form action="save.php" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                
                <!-- 1. ຂໍ້ມູນແພັກເກັດ ແລະ ກຳນົດວັນທີ -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-primary">
                            <i class="fas fa-info-circle me-2"></i>1. ຂໍ້ມູນແພັກເກັດ ແລະ ກຳນົດວັນທີ
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">ລະຫັດແພັກເກັດ</label>
                                <input type="text" name="tour_code" class="form-control bg-light border-0 py-2 shadow-none" placeholder="ຕົວຢ່າງ: PKG-001" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold small">ຊື່ແພັກເກັດທົວ</label>
                                <input type="text" name="tour_name" class="form-control bg-light border-0 py-2 shadow-none" placeholder="ຊື່ແພັກເກັດ..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ໝວດໝູ່</label>
                                <select name="category" class="form-select bg-light border-0 py-2 shadow-none">
                                    <option value="ທົວວັດທະນະທຳ">ທົວວັດທະນະທຳ</option>
                                    <option value="ທົວຜະຈົນໄພ">ທົວຜະຈົນໄພ</option>
                                    <option value="ທົວຄອບຄົວ">ທົວຄອບຄົວ</option>
                                    <option value="ທົວພັກຜ່ອນ">ທົວພັກຜ່ອນ</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-primary">ລາຄາຂາຍ/ທ່ານ (ກີບ)</label>
                                <input type="number" name="price" class="form-control border-primary py-2 shadow-none" placeholder="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-danger">ວັນທີເລີ່ມເດີນທາງ</label>
                                <input type="date" name="start_date" class="form-control border-danger py-2 shadow-none" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-danger">ວັນທີສິ້ນສຸດ</label>
                                <input type="date" name="end_date" class="form-control border-danger py-2 shadow-none" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">ໄລຍະເວລາ</label>
                                <input type="text" name="duration" class="form-control bg-light border-0 py-2 shadow-none" placeholder="ຕົວຢ່າງ: 2 ວັນ 1 ຄືນ">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">ບ່ອນນັ່ງສູງສຸດ</label>
                                <input type="number" name="max_seats" class="form-control bg-light border-0 py-2 shadow-none" value="10" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">ອາຫານ (ຄາບ)</label>
                                <input type="number" name="meals" class="form-control bg-light border-0 py-2 shadow-none" value="0">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ສະຖານທີ່ນັດພົບ (Meeting Point)</label>
                                <input type="text" name="meeting_point" class="form-control bg-light border-0 py-2 shadow-none" placeholder="ບອກບ່ອນຂຶ້ນລົດ ຫຼື ຈຸດນັດພົບ...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ຮູບພາບປະກອບ -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-warning">
                            <i class="fas fa-image me-2"></i>2. ຮູບພາບປະກອບ
                        </h5>
                        <div class="mb-4">
                            <label class="form-label fw-bold small">ຮູບໜ້າປົກ (Cover Image)</label>
                            <input type="file" name="image" class="form-control bg-light border-0 small shadow-none" accept="image/*" required>
                            <small class="text-muted mt-1 d-block">ຮູບຫຼັກທີ່ຈະໂຊຢູ່ໜ້າເວັບ</small>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small">ຮູບ Gallery (ເລືອກໄດ້ຫຼາຍຮູບ)</label>
                            <input type="file" name="gallery[]" class="form-control bg-light border-0 small shadow-none" accept="image/*" multiple>
                            <small class="text-muted mt-1 d-block">ຮູບພາບລາຍລະອຽດເພີ່ມເຕີມ</small>
                        </div>
                    </div>
                </div>

                <!-- 3. ລາຍລະອຽດສິ່ງທີ່ລວມ ແລະ ແຜນການ -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4 text-dark">
                            <i class="fas fa-list me-2"></i>3. ລາຍລະອຽດແຜນການເດີນທາງ
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">ຈຸດເດັ່ນ (Highlights)</label>
                                <textarea name="highlights" class="form-control bg-light border-0 shadow-none" rows="4" placeholder="ສິ່ງທີ່ລູກຄ້າຈະໄດ້ພົບໃນທົວນີ້..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-success">ສິ່ງທີ່ລວມຢູ່ນຳ</label>
                                <textarea name="whats_included" class="form-control bg-light border-0 shadow-none" rows="4" placeholder="ຄ່າລົດ, ຄ່າອາຫານ, ປະກັນໄພ..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-danger">ສິ່ງທີ່ບໍ່ລວມຢູ່ນຳ</label>
                                <textarea name="whats_excluded" class="form-control bg-light border-0 shadow-none" rows="4" placeholder="ຄ່າໃຊ້ຈ່າຍສ່ວນຕົວ, ທິບ..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">ແຜນການເດີນທາງລະອຽດ (Itinerary)</label>
                                <textarea name="itinerary" class="form-control bg-light border-0 shadow-none" rows="6" placeholder="ມື້ທີ 1: ... &#10;ມື້ທີ 2: ..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ປຸ່ມບັນທຶກ -->
                <div class="col-12 text-center mt-5 mb-5">
                    <button type="submit" name="save_tour" class="btn btn-primary btn-lg px-5 rounded-pill shadow fw-bold">
                        <i class="fas fa-save me-2"></i> ບັນທຶກແພັກເກັດທົວ
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<style>
    /* ຕົບແຕ່ງພິເສດເພື່ອຄວາມງາມ */
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        border: 1px solid #0d6efd !important;
    }
    textarea {
        resize: none;
    }
</style>

<?php include '../../includes/footer.php'; ?>