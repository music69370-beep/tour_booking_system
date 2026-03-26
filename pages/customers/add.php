<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <!-- ເອີ້ນໃຊ້ Navbar (ຂໍ້ມູນຜູ້ໃຊ້ຈະຢູ່ມຸມຂວາເທິງ) -->
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark">
                <i class="fas fa-user-plus text-success me-2"></i>ເພີ່ມຂໍ້ມູນລູກຄ້າໃໝ່
            </h2>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <form action="save.php" method="POST">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ຊື່ ແລະ ນາມສະກຸນ</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="fullname" class="form-control bg-light border-0 shadow-none" placeholder="ປ້ອນຊື່ລູກຄ້າ..." required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ເບີໂທລະສັບ</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-phone text-muted"></i></span>
                            <input type="text" name="phone" class="form-control bg-light border-0 shadow-none" placeholder="020..." required>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold small">ອີເມວ (ຖ້າມີ)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control bg-light border-0 shadow-none" placeholder="example@gmail.com">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold small">ທີ່ຢູ່ປະຈຸບັນ</label>
                        <textarea name="address" class="form-control bg-light border-0 shadow-none" rows="4" placeholder="ບ້ານ, ເມືອງ, ແຂວງ..."></textarea>
                    </div>

                    <div class="col-12 mt-5 pt-3 border-top text-end">
                        <button type="submit" name="save_customer" class="btn btn-success px-5 rounded-pill shadow">
                            <i class="fas fa-save me-2"></i> ບັນທຶກຂໍ້ມູນລູກຄ້າ
                        </button>
                        <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
    /* ຕົບແຕ່ງ Input Group ໃຫ້ເບິ່ງທັນສະໄໝ */
    .input-group-text {
        border-radius: 10px 0 0 10px !important;
    }
    .form-control {
        border-radius: 0 10px 10px 0 !important;
        padding: 10px 15px;
    }
    textarea.form-control {
        border-radius: 10px !important;
    }
</style>

<?php include '../../includes/footer.php'; ?>