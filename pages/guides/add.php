<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-user-plus text-primary me-2"></i>ເພີ່ມໄກ້ຜູ້ນຳທ່ຽວໃໝ່</h2>
        </div>

        <form action="save.php" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                
                <!-- 1. Professional Info -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-id-card me-2"></i>1. ຂໍ້ມູນດ້ານວິຊາຊີບ</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ຊື່ ແລະ ນາມສະກຸນ</label>
                                <input type="text" name="fullname" class="form-control bg-light border-0 shadow-none" placeholder="ປ້ອນຊື່ ແລະ ນາມສະກຸນ..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ເລກບັດຜູ້ນຳທ່ຽວ</label>
                                <input type="text" name="license_id" class="form-control bg-light border-0 shadow-none" placeholder="G-XXXXX" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ວັນໝົດອາຍຸບັດ</label>
                                <input type="date" name="license_expiry" class="form-control bg-light border-0 shadow-none" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ພາສາທີ່ເວົ້າໄດ້</label>
                                <input type="text" name="languages" class="form-control bg-light border-0 shadow-none" placeholder="ລາວ, ອັງກິດ, ຈີນ..." required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold small">ຄວາມຊຳນານ (Specialization)</label>
                                <input type="text" name="specialization" class="form-control bg-light border-0 shadow-none" placeholder="ປະຫວັດສາດ, ຜະຈົນໄພ...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">ປະສົບການ (ປີ)</label>
                                <input type="number" name="exp_years" class="form-control bg-light border-0 shadow-none" value="1">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Contact & Documents -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-success"><i class="fas fa-envelope me-2"></i>2. ຂໍ້ມູນຕິດຕໍ່ ແລະ ເອກະສານ</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ເບີໂທລະສັບ</label>
                                <input type="text" name="phone" class="form-control bg-light border-0 shadow-none" placeholder="020..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ອີເມວ</label>
                                <input type="email" name="email" class="form-control bg-light border-0 shadow-none" placeholder="example@mail.com">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ທີ່ຢູ່ປະຈຸບັນ</label>
                                <textarea name="address" class="form-control bg-light border-0 shadow-none" rows="2" placeholder="ບ້ານ, ເມືອງ, ແຂວງ..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຮູບປະຈຳຕົວ</label>
                                <input type="file" name="image" class="form-control bg-light border-0 shadow-none small" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ເອກະສານຕິດຄັດ (PDF/ຮູບ)</label>
                                <input type="file" name="doc_attachment" class="form-control bg-light border-0 shadow-none small">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Payment Information (ຈຸດທີ່ແກ້ໄຂເປັນ Dropdown) -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-warning"><i class="fas fa-wallet me-2"></i>3. ຂໍ້ມູນການຊຳລະເງິນ</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ເລືອກທະນາຄານ</label>
                                <select name="bank_name" class="form-select bg-light border-0 shadow-none" required>
                                    <option value="">-- ເລືອກທະນາຄານ --</option>
                                    <option value="BCEL">ທະນາຄານການຄ້າຕ່າງປະເທດລາວ (BCEL)</option>
                                    <option value="LDB">ທະນາຄານພັດທະນາລາວ (LDB)</option>
                                    <option value="JDB">ທະນາຄານຮ່ວມທຸລະກິດ ລາວ-ຫວຽດ (LVB)</option>
                                    <option value="STB">ທະນາຄານເອັສທີ (STB)</option>
                                    <option value="PSB">ທະນາຄານພົງສະຫວັນ (PSB)</option>
                                    <option value="APBL">ທະນາຄານສົ່ງເສີມກະສິກຳ (APB)</option>
                                    <option value="BIC">ທະນາຄານ ບີໄອຊີ (BIC)</option>
                                    <option value="Maruhan">ທະນາຄານ ມາຣູຮານ (Maruhan Japan Bank)</option>
                                    <option value="Indochina">ທະນາຄານ ອິນໂດຈີນ (IB)</option>
                                    <option value="Plus">ທະນາຄານ ພັດທະນາກະສິກຳ (Plus Bank)</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ເລກບັນຊີທະນາຄານ</label>
                                <input type="text" name="bank_account" class="form-control bg-light border-0 shadow-none" placeholder="000-00-00-00000000-000">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Emergency & Health -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-danger"><i class="fas fa-first-aid me-2"></i>4. ຂໍ້ມູນສຸກເສີນ ແລະ ສຸຂະພາບ</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຊື່ຜູ້ຕິດຕໍ່ສຸກເສີນ</label>
                                <input type="text" name="emergency_name" class="form-control bg-light border-0 shadow-none" placeholder="ຊື່ ແລະ ນາມສະກຸນ...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ເບີໂທສຸກເສີນ</label>
                                <input type="text" name="emergency_phone" class="form-control bg-light border-0 shadow-none" placeholder="020...">
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input shadow-none" type="checkbox" name="first_aid_certified" value="1" id="aidCheck">
                                    <label class="form-check-label fw-bold text-dark" for="aidCheck">ຜ່ານການຢັ້ງຢືນການປະຖົມພະຍາບານ (First Aid Certified)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-5 mb-5">
                    <button type="submit" name="save_guide" class="btn btn-primary btn-lg px-5 rounded-pill shadow">
                        <i class="fas fa-save me-2"></i> ບັນທຶກຂໍ້ມູນໄກ້
                    </button>
                    <a href="index.php" class="btn btn-light border btn-lg px-5 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>

<style>
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        border: 1.5px solid #0d6efd !important;
    }
</style>

<?php include '../../includes/footer.php'; ?>