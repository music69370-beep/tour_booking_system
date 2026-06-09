<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-bus text-info me-2"></i>ເພີ່ມຂໍ້ມູນລົດທົວໃໝ່</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍ້ອນກັບ</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
            <form action="save.php" method="POST">
                <div class="row g-4">
                    <!-- ຂໍ້ມູນລົດ -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ລຸ້ນລົດ (Vehicle Model)</label>
                        <input type="text" name="model" class="form-control bg-light border-0 py-2 shadow-none" placeholder="ຕົວຢ່າງ: Toyota Hiace 2023" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ເລກທະບຽນ (Plate Number)</label>
                        <input type="text" name="plate_number" class="form-control bg-light border-0 py-2 shadow-none" placeholder="ຕົວຢ່າງ: ກກ 1234" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small">ປະເພດລົດ</label>
                        <select name="vehicle_type" class="form-select bg-light border-0 py-2 shadow-none">
                            <option value="Van">ລົດຕູ້ (Van)</option>
                            <option value="Bus">ລົດບັດ (Bus)</option>
                            <option value="SUV">ລົດ SUV</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small">ຈຳນວນບ່ອນນັ່ງ</label>
                        <input type="number" name="capacity" class="form-control bg-light border-0 py-2 shadow-none" value="15" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-danger">ວັນໝົດອາຍຸປະກັນໄພ</label>
                        <input type="date" name="insurance_expiry" class="form-control border-danger py-2 shadow-none" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold small">ອຸປະກອນເສີມ (Amenities)</label>
                        <textarea name="amenities" class="form-control bg-light border-0 shadow-none" rows="3" placeholder="Wi-Fi, ຕູ້ຢັນ, ເບາະນວດ, etc."></textarea>
                    </div>

                    <div class="col-12 text-center mt-5 pt-3 border-top">
                        <button type="submit" name="save_vehicle" class="btn btn-info text-white btn-lg px-5 rounded-pill shadow">
                            <i class="fas fa-save me-2"></i> ບັນທຶກຂໍ້ມູນລົດ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>