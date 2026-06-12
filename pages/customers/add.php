<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-user-plus text-success me-2"></i>ເພີ່ມຂໍ້ມູນລູກຄ້າ (ພ້ອມຮູບເອກະສານ)</h2>
        </div>

        <!-- ເພີ່ມ enctype ເພື່ອໃຫ້ສົ່ງໄຟລ໌ຮູບໄດ້ -->
        <form action="save.php" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                
                <!-- 1. ຂໍ້ມູນສ່ວນຕົວ -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-id-card me-2"></i>1. ຂໍ້ມູນສ່ວນຕົວ & ຕົວຕົນ</h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">ຊື່ ແລະ ນາມສະກຸນ</label>
                                <input type="text" name="fullname" class="form-control bg-light border-0 py-2" placeholder="ຊື່ແທ້..." required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">ເພດ</label>
                                <select name="gender" class="form-select bg-light border-0 py-2">
                                    <option value="Male">ຊາຍ (Male)</option>
                                    <option value="Female">ຍິງ (Female)</option>
                                    <option value="Other">ອື່ນໆ</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ວັນເດືອນປີເກີດ</label>
                                <input type="date" name="birthday" class="form-control bg-light border-0 py-2">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ສັນຊາດ</label>
                                <input type="text" name="nationality" class="form-control bg-light border-0 py-2" value="Lao">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ເລກບັດປະຈຳຕົວ / ພາສປອດ</label>
                                <input type="text" name="id_card_no" class="form-control bg-light border-0 py-2" placeholder="ID Number...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-primary">ຮູບພາບບັດ ຫຼື ພາສປອດ (Scan/ຮູບຖ່າຍ)</label>
                                <input type="file" name="id_card_image" class="form-control bg-light border-0 py-2 small" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ເບີໂທລະສັບ</label>
                                <input type="text" name="phone" class="form-control bg-light border-0 py-2" placeholder="020..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ອີເມວ</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2" placeholder="example@gmail.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ຂໍ້ມູນການຕິດຕໍ່ສຸກເສີນ & ທີ່ຢູ່ -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-danger"><i class="fas fa-map-marker-alt me-2"></i>2. ທີ່ຢູ່ & ຕິດຕໍ່ສຸກເສີນ</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">ທີ່ຢູ່ປະຈຸບັນ</label>
                                <textarea name="address" class="form-control bg-light border-0 py-2" rows="3" placeholder="ບ້ານ, ເມືອງ, ແຂວງ..."></textarea>
                            </div>
                            <div class="col-md-12 mt-4"><hr><h6 class="fw-bold text-danger mb-3">ບຸກຄົນທີ່ຕິດຕໍ່ໄດ້ສຸກເສີນ</h6></div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">ຊື່ຜູ້ຕິດຕໍ່ສຸກເສີນ</label>
                                <input type="text" name="emergency_name" class="form-control bg-light border-0 py-2" placeholder="ຊື່ຍາດພີ່ນ້ອງ...">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">ເບີໂທສຸກເສີນ</label>
                                <input type="text" name="emergency_phone" class="form-control bg-light border-0 py-2" placeholder="ເບີໂທ...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-5 mb-5">
                    <button type="submit" name="save_customer" class="btn btn-success btn-lg px-5 rounded-pill shadow">
                        <i class="fas fa-save me-2"></i> ບັນທຶກຂໍ້ມູນລູກຄ້າ
                    </button>
                    <a href="index.php" class="btn btn-light border btn-lg px-5 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>