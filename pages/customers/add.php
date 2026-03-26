<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold text-dark"><i class="fas fa-user-plus text-success me-2"></i>ເພີ່ມລູກຄ້າໃໝ່</h2>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="save.php" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">ຊື່ ແລະ ນາມສະກຸນ</label>
                    <input type="text" name="fullname" class="form-control" placeholder="ປ້ອນຊື່ລູກຄ້າ..." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">ເບີໂທລະສັບ</label>
                    <input type="text" name="phone" class="form-control" placeholder="020..." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">ອີເມວ</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">ທີ່ຢູ່</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="ບ້ານ, ເມືອງ, ແຂວງ..."></textarea>
                </div>
                
                <!-- ປຸ່ມບັນທຶກ ຢູ່ບ່ອນນີ້ -->
                <div class="col-12 mt-4 pt-3 border-top">
                    <button type="submit" name="save_customer" class="btn btn-success px-5 rounded-pill shadow-sm">
                        <i class="fas fa-save me-2"></i> ບັນທຶກຂໍ້ມູນລູກຄ້າ
                    </button>
                    <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>