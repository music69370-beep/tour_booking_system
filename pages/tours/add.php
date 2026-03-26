<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold text-primary"><i class="fas fa-plus-circle me-2"></i>ເພີ່ມແພັກເກັດທົວໃໝ່</h2>
    </div>
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="save.php" method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">ຊື່ແພັກເກັດທົວ</label>
                    <input type="text" name="tour_name" class="form-control" placeholder="ຕົວຢ່າງ: ທ່ຽວຫຼວງພະບາງ" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">ລາຄາ (ກີບ)</label>
                    <input type="number" name="price" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">ໄລຍະເວລາ</label>
                    <input type="text" name="duration" class="form-control" placeholder="3 ມື້ 2 ຄືນ">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">ຈຳນວນຄາບອາຫານ</label>
                    <input type="number" name="meals" class="form-control" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">ບ່ອນນັ່ງທັງໝົດ</label>
                    <input type="number" name="max_seats" class="form-control" value="10">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">ຮູບພາບປະກອບ</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold text-danger">ລາຍລະອຽດແຜນການເດີນທາງ (ມື້ທີ 1, 2, 3... ແລະ ເວລາ)</label>
                    <textarea name="itinerary" class="form-control" rows="5" placeholder="ຕົວຢ່າງ: &#10;08:00 - ເດີນທາງອອກຈາກວຽງຈັນ&#10;12:00 - ກິນເຂົ້າສວຍຢູ່ເຂື່ອນນ້ຳງື່ມ..."></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold text-success">ກິດຈະກຳຫຼັກ (Activities)</label>
                    <textarea name="activities" class="form-control" rows="3" placeholder="ຕົວຢ່າງ: ຂີ່ເຮືອຊົມວິວ, ຕັກບາດຍາມເຊົ້າ, ທ່ຽວຕາດກວາງຊີ..."></textarea>
                </div>
                <div class="col-12 mt-4 text-end">
                    <button type="submit" name="save_tour" class="btn btn-primary px-5 rounded-pill shadow">ບັນທຶກຂໍ້ມູນທົວ</button>
                    <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>