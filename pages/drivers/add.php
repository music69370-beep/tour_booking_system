<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-user-tie text-success me-2"></i>ເພີ່ມຂໍ້ມູນຄົນຂັບ</h2>
        </div>

        <form action="save.php" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                
                <!-- 1. ຂໍ້ມູນສ່ວນຕົວ -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-id-card me-2"></i>1. ຂໍ້ມູນສ່ວນຕົວ</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">ຊື່ ແລະ ນາມສະກຸນ</label>
                                <input type="text" name="fullname" class="form-control bg-light border-0" placeholder="ຊື່ແທ້..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ເບີໂທລະສັບ</label>
                                <input type="text" name="phone" class="form-control bg-light border-0" placeholder="020..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ເລກບັດປະຈຳຕົວ</label>
                                <input type="text" name="id_card_no" class="form-control bg-light border-0">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">ທີ່ຢູ່ປະຈຸບັນ</label>
                                <textarea name="address" class="form-control bg-light border-0" rows="2"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-danger">ເບີຕິດຕໍ່ສຸກເສີນ</label>
                                <input type="text" name="emergency_phone" class="form-control bg-light border-0" placeholder="ເບີຍາດພີ່ນ້ອງ...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ຂໍ້ມູນດ້ານວິຊາຊີບ & ເອກະສານ -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-success"><i class="fas fa-file-contract me-2"></i>2. ຂໍ້ມູນວິຊາຊີບ & ເອກະສານ</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ເລກທີໃບຂັບຂີ່</label>
                                <input type="text" name="license_number" class="form-control bg-light border-0" placeholder="No. XXXX" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ປະເພດໃບຂັບຂີ່</label>
                                <select name="license_type" class="form-select bg-light border-0">
                                    <option value="B">ປະເພດ ຂ (ລົດເບົາ)</option>
                                    <option value="C">ປະເພດ ຄ (ລົດບັນທຸກ)</option>
                                    <option value="D">ປະເພດ ງ (ລົດເມ)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ວັນໝົດອາຍຸໃບຂັບຂີ່</label>
                                <input type="date" name="license_expiry" class="form-control border-danger" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ປະສົບການ (ປີ)</label>
                                <input type="number" name="experience_years" class="form-control bg-light border-0" value="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-primary">ຮູບຖ່າຍຄົນຂັບ</label>
                                <input type="file" name="image" class="form-control bg-light border-0 small" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-primary">ຮູບໃບຂັບຂີ່ (Scan)</label>
                                <input type="file" name="license_image" class="form-control bg-light border-0 small" accept="image/*">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-primary">ຮູບຖ່າຍບັດປະຈຳຕົວ (Scan/ຮູບຖ່າຍ)</label>
                                <input type="file" name="id_card_image" class="form-control bg-light border-0 small" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-5">
                    <button type="submit" name="btn_save" class="btn btn-success btn-lg px-5 rounded-pill shadow">
                        <i class="fas fa-save me-2"></i> ບັນທຶກຂໍ້ມູນຄົນຂັບ
                    </button>
                    <a href="index.php" class="btn btn-light border btn-lg px-5 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>