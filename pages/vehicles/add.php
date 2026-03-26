<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <!-- ເອີ້ນໃຊ້ Navbar -->
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-bus text-info me-2"></i>ເພີ່ມຂໍ້ມູນລົດ ແລະ ຄົນຂັບໃໝ່</h2>
        </div>

        <form action="save.php" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <!-- ສ່ວນທີ 1: ຂໍ້ມູນລົດ -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-info"><i class="fas fa-car me-2"></i>1. ຂໍ້ມູນລົດ (Vehicle Info)</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ລຸ້ນລົດ (Model)</label>
                                <input type="text" name="model" class="form-control bg-light border-0 shadow-none" placeholder="Toyota Hiace 2023" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ເລກທະບຽນ</label>
                                <input type="text" name="plate_number" class="form-control bg-light border-0 shadow-none" placeholder="ກກ 1234" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ປະເພດລົດ</label>
                                <select name="vehicle_type" class="form-select bg-light border-0 shadow-none">
                                    <option value="Van">ລົດຕູ້ (Van)</option>
                                    <option value="Bus">ລົດບັດ (Bus)</option>
                                    <option value="SUV">ລົດ SUV</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຈຳນວນບ່ອນນັ່ງ</label>
                                <input type="number" name="capacity" class="form-control bg-light border-0 shadow-none" value="15" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ວັນໝົດອາຍຸປະກັນໄພ</label>
                                <input type="date" name="insurance_expiry" class="form-control bg-light border-0 shadow-none" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ອຸປະກອນເສີມ (Amenities)</label>
                                <input type="text" name="amenities" class="form-control bg-light border-0 shadow-none" placeholder="Wi-Fi, ຕູ້ຢັນ, etc.">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ສ່ວນທີ 2: ຂໍ້ມູນຄົນຂັບ -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-success"><i class="fas fa-user-tie me-2"></i>2. ຂໍ້ມູນຄົນຂັບ (Driver Info)</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຊື່ຄົນຂັບ</label>
                                <input type="text" name="driver_name" class="form-control bg-light border-0 shadow-none" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ເບີໂທຄົນຂັບ</label>
                                <input type="text" name="driver_phone" class="form-control bg-light border-0 shadow-none" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ເລກທີໃບຂັບຂີ່</label>
                                <input type="text" name="license_number" class="form-control bg-light border-0 shadow-none" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ວັນໝົດອາຍຸໃບຂັບຂີ່</label>
                                <input type="date" name="license_expiry" class="form-control bg-light border-0 shadow-none" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-primary">ຮູບຄົນຂັບ</label>
                                <input type="file" name="driver_image" class="form-control bg-light border-0 shadow-none small" accept="image/*">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-primary">ຮູບໃບຂັບຂີ່</label>
                                <input type="file" name="license_image" class="form-control bg-light border-0 shadow-none small" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-5">
                    <button type="submit" name="save_vehicle" class="btn btn-info text-white btn-lg px-5 rounded-pill shadow">
                        <i class="fas fa-save me-2"></i> ບັນທຶກຂໍ້ມູນທັງໝົດ
                    </button>
                    <a href="index.php" class="btn btn-light border btn-lg px-5 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>