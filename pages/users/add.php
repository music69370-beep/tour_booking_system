<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-user-plus text-danger me-2"></i>ເພີ່ມພະນັກງານໃໝ່</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍົກເລີກ</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <form action="save.php" method="POST" enctype="multipart/form-data">
                
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs bg-light border-0 px-3 pt-3" id="userTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold border-0" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button"><i class="fas fa-user me-2"></i>ຂໍ້ມູນສ່ວນຕົວ</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold border-0" id="work-tab" data-bs-toggle="tab" data-bs-target="#work" type="button"><i class="fas fa-briefcase me-2"></i>ການຈ້າງງານ</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold border-0" id="legal-tab" data-bs-toggle="tab" data-bs-target="#legal" type="button"><i class="fas fa-file-contract me-2"></i>ເອກະສານ & ສຸກເສີນ</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold border-0" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button"><i class="fas fa-shield-alt me-2"></i>ບັນຊີຜູ້ໃຊ້</button>
                    </li>
                </ul>

                <div class="tab-content p-4 p-md-5" id="userTabContent">
                    
                    <!-- 1. Personal Information -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ຊື່ ແລະ ນາມສະກຸນ</label>
                                <input type="text" name="fullname" class="form-control bg-light border-0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">ເບີໂທລະສັບ</label>
                                <input type="text" name="phone" class="form-control bg-light border-0" placeholder="020..." required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">ວັນເດືອນປີເກີດ</label>
                                <input type="date" name="dob" class="form-control bg-light border-0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ອີເມວ</label>
                                <input type="email" name="email" class="form-control bg-light border-0" placeholder="example@gmail.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">ຮູບໂປຣຟາຍ</label>
                                <input type="file" name="profile_pic" class="form-control bg-light border-0" accept="image/*">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">ທີ່ຢູ່ປະຈຸບັນ</label>
                                <textarea name="address" class="form-control bg-light border-0" rows="3" placeholder="ບ້ານ, ເມືອງ, ແຂວງ..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Employment Details -->
                    <div class="tab-pane fade" id="work" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">ລະຫັດພະນັກງານ (Employee ID)</label>
                                <input type="text" name="employee_code" class="form-control bg-light border-0" placeholder="EMP-001" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">ຕຳແໜ່ງ</label>
                                <input type="text" name="job_title" class="form-control bg-light border-0" placeholder="Admin, Sales, Guide..." required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">ພະແນກ</label>
                                <select name="department" class="form-select bg-light border-0">
                                    <option value="ບໍລິການ">ພະແນກບໍລິການ</option>
                                    <option value="ບັນຊີ">ພະແນກບັນຊີ</option>
                                    <option value="ຂົນສົ່ງ">ພະແນກຂົນສົ່ງ</option>
                                    <option value="ການຕະຫຼາດ">ພະແນກການຕະຫຼາດ</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">ວັນທີເລີ່ມວຽກ</label>
                                <input type="date" name="date_joined" class="form-control bg-light border-0" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- 3. Legal & Documents -->
                    <div class="tab-pane fade" id="legal" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ເລກບັດປະຈຳຕົວ / ສຳເນົາສຳມະໂນຄົວ</label>
                                <input type="text" name="id_card_no" class="form-control bg-light border-0">
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-danger">ຊື່ຜູ້ຕິດຕໍ່ສຸກເສີນ</label>
                                <input type="text" name="emergency_name" class="form-control bg-light border-0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-danger">ເບີໂທສຸກເສີນ</label>
                                <input type="text" name="emergency_phone" class="form-control bg-light border-0">
                            </div>
                        </div>
                    </div>

                    <!-- 4. Security & Account -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">ຊື່ຜູ້ໃຊ້ງານ (Username)</label>
                                <input type="text" name="username" class="form-control border-primary" placeholder="ໃຊ້ສຳລັບ Login..." required>
                                <small class="text-muted">ໃຊ້ສຳລັບເຂົ້າລະບົບ (ຫ້າມຊ້ຳກັນ)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">ລະຫັດຜ່ານ (Password)</label>
                                <input type="password" name="password" class="form-control border-primary" required>
                                <small class="text-muted">ລະຫັດຈະຖືກເຂົ້າລະຫັດປອດໄພ (Bcrypt)</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">ລະດັບສິດ</label>
                                <select name="role" class="form-select bg-light border-0">
                                    <option value="Staff">Staff (ພະນັກງານ)</option>
                                    <option value="Admin">Admin (ແອດມິນ)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer bg-white border-top p-4 text-center">
                    <button type="submit" name="save_user" class="btn btn-danger btn-lg px-5 rounded-pill shadow">
                        <i class="fas fa-save me-2"></i> ບັນທຶກຂໍ້ມູນພະນັກງານ
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
    .nav-tabs .nav-link { color: #6c757d; padding: 15px 25px; transition: 0.3s; }
    .nav-tabs .nav-link.active { color: #dc3545; background: #fff !important; border-bottom: 3px solid #dc3545 !important; }
    .nav-tabs .nav-link:hover { color: #dc3545; }
</style>

<?php include '../../includes/footer.php'; ?>