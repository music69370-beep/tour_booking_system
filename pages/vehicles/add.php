<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold">ເພີ່ມລົດທົວໃໝ່</h2>
    </div>
    <div class="card border-0 shadow-sm rounded-4 p-4" style="max-width: 700px;">
        <form action="save.php" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">ລຸ້ນລົດ (Model)</label>
                    <input type="text" name="model" class="form-control" placeholder="ເຊັ່ນ: Toyota Hiace 2023" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">ເລກທະບຽນ (Plate Number)</label>
                    <input type="text" name="plate_number" class="form-control" placeholder="ກກ 1234" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">ຈຳນວນບ່ອນນັ່ງ</label>
                    <input type="number" name="capacity" class="form-control" value="15" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">ຊື່ຄົນຂັບ</label>
                    <input type="text" name="driver_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">ເບີໂທຄົນຂັບ</label>
                    <input type="text" name="driver_phone" class="form-control" required>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" name="save_vehicle" class="btn btn-info text-white px-5 rounded-pill shadow">ບັນທຶກຂໍ້ມູນລົດ</button>
                    <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>