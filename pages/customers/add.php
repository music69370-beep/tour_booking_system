<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold">ເພີ່ມລູກຄ້າໃໝ່</h2>
    </div>
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="save.php" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">ຊື່ ແລະ ນາມສະກຸນ</label>
                    <input type="text" name="fullname" class="form-control" placeholder="ປ້ອນຊື່ລູກຄ້າ..." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ເບີໂທລະສັບ</label>
                    <input type="text" name="phone" class="form-control" placeholder="020..." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ອີເມວ</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com">
                </div>
                <div class="col-md-12">
                    <label class="form-label">ທີ່ຢູ່</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="ບ້ານ, ເມືອງ, ແຂວງ..."></textarea>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" name="save_customer" class="btn btn-success px-5">ບັນທຶກຂໍ້ມູນ</button>
                    <a href="index.php" class="btn btn-light border px-4">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>