<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <!-- ເອີ້ນໃຊ້ Navbar (ເພື່ອໂຊຊື່ຜູ້ໃຊ້ງານມຸມຂວາເທິງ) -->
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark">
                <i class="fas fa-user-shield text-danger me-2"></i>ເພີ່ມບັນຊີຜູ້ໃຊ້ໃໝ່
            </h2>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <form action="save.php" method="POST">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ຊື່ເຕັມ (Full Name)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-id-card text-muted"></i></span>
                                    <input type="text" name="fullname" class="form-control bg-light border-0 shadow-none" placeholder="ປ້ອນຊື່ ແລະ ນາມສະກຸນ..." required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ຊື່ຜູ້ໃຊ້ (Username)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-user-circle text-muted"></i></span>
                                    <input type="text" name="username" class="form-control bg-light border-0 shadow-none" placeholder="ໃຊ້ສຳລັບ Login..." required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ລະຫັດຜ່ານ (Password)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-key text-muted"></i></span>
                                    <input type="password" name="password" class="form-control bg-light border-0 shadow-none" placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ລະດັບສິດ (Role)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-user-tag text-muted"></i></span>
                                    <select name="role" class="form-select bg-light border-0 shadow-none">
                                        <option value="Staff">Staff (ພະນັກງານ)</option>
                                        <option value="Admin">Admin (ຜູ້ດູແລລະບົບ)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 mt-5 pt-3 border-top">
                                <button type="submit" name="save_user" class="btn btn-danger px-5 rounded-pill shadow">
                                    <i class="fas fa-save me-2"></i> ບັນທຶກຜູ້ໃຊ້
                                </button>
                                <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">
                                    ຍົກເລີກ
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- ສ່ວນແນະນຳເພີ່ມເຕີມດ້ານຂ້າງ -->
            <div class="col-lg-5 ms-lg-auto">
                <div class="alert alert-info border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold"><i class="fas fa-info-circle me-2"></i>ຄຳແນະນຳ</h5>
                    <ul class="mb-0 small">
                        <li class="mb-2"><b>Staff:</b> ສາມາດຈັດການການຈອງ, ລູກຄ້າ ແລະ ລົດທົວໄດ້.</li>
                        <li><b>Admin:</b> ສາມາດຈັດການທຸກຢ່າງໃນລະບົບ ລວມທັງການເພີ່ມ/ລຶບ ຜູ້ໃຊ້ງານ.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    /* ຕົບແຕ່ງ Input Group ໃຫ້ເບິ່ງທັນສະໄໝ */
    .input-group-text {
        border-radius: 12px 0 0 12px !important;
        border: 1px solid #f8f9fa;
    }
    .form-control, .form-select {
        border-radius: 0 12px 12px 0 !important;
        padding: 12px 15px;
    }
    /* ປັບຂະໜາດ Card ໃຫ້ເໝາະສົມ */
    .card {
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
</style>

<?php include '../../includes/footer.php'; ?>