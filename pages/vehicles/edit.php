<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$id = mysqli_real_escape_string($conn, $_GET['id']);
$res = mysqli_query($conn, "SELECT * FROM vehicles WHERE vehicle_id = '$id'");
$row = mysqli_fetch_assoc($res);

if (!$row) { echo "ບໍ່ພົບຂໍ້ມູນ"; exit; }
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold text-warning"><i class="fas fa-edit me-2"></i>ແກ້ໄຂຂໍ້ມູນລົດ ແລະ ຄົນຂັບ</h2>
    </div>

    <form action="update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="vehicle_id" value="<?php echo $row['vehicle_id']; ?>">
        <!-- ຈື່ຊື່ຮູບເກົ່າໄວ້ ຖ້າບໍ່ມີການປ່ຽນໃໝ່ -->
        <input type="hidden" name="old_driver_image" value="<?php echo $row['driver_image']; ?>">
        <input type="hidden" name="old_license_image" value="<?php echo $row['license_image']; ?>">

        <div class="row g-4">
            <!-- ຂໍ້ມູນລົດ -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4 text-info">1. ຂໍ້ມູນລົດ</h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">ລຸ້ນລົດ (Model)</label>
                            <input type="text" name="model" class="form-control bg-light border-0" value="<?php echo $row['model']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">ເລກທະບຽນ</label>
                            <input type="text" name="plate_number" class="form-control bg-light border-0" value="<?php echo $row['plate_number']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">ປະເພດລົດ</label>
                            <select name="vehicle_type" class="form-select bg-light border-0">
                                <option value="Van" <?php if($row['vehicle_type']=='Van') echo 'selected'; ?>>Van</option>
                                <option value="Bus" <?php if($row['vehicle_type']=='Bus') echo 'selected'; ?>>Bus</option>
                                <option value="SUV" <?php if($row['vehicle_type']=='SUV') echo 'selected'; ?>>SUV</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">ຈຳນວນບ່ອນນັ່ງ</label>
                            <input type="number" name="capacity" class="form-control bg-light border-0" value="<?php echo $row['capacity']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">ວັນໝົດອາຍຸປະກັນໄພ</label>
                            <input type="date" name="insurance_expiry" class="form-control bg-light border-0" value="<?php echo $row['insurance_expiry']; ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ຂໍ້ມູນຄົນຂັບ -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4 text-success">2. ຂໍ້ມູນຄົນຂັບ</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">ຊື່ຄົນຂັບ</label>
                            <input type="text" name="driver_name" class="form-control bg-light border-0" value="<?php echo $row['driver_name']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">ເບີໂທ</label>
                            <input type="text" name="driver_phone" class="form-control bg-light border-0" value="<?php echo $row['driver_phone']; ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">ປ່ຽນຮູບຄົນຂັບ (ຖ້າຕ້ອງການ)</label>
                            <input type="file" name="driver_image" class="form-control border-0 bg-light small" accept="image/*">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">ສະຖານະລົດ</label>
                            <select name="status" class="form-select bg-light border-0">
                                <option value="Available" <?php if($row['status']=='Available') echo 'selected'; ?>>Available</option>
                                <option value="Busy" <?php if($row['status']=='Busy') echo 'selected'; ?>>Busy</option>
                                <option value="Maintenance" <?php if($row['status']=='Maintenance') echo 'selected'; ?>>Maintenance</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 text-center mt-5 mb-5">
                <button type="submit" name="update_vehicle" class="btn btn-warning btn-lg px-5 rounded-pill shadow">
                    <i class="fas fa-save me-2"></i> ບັນທຶກການແກ້ໄຂ
                </button>
                <a href="index.php" class="btn btn-light border btn-lg px-5 rounded-pill ms-2">ຍົກເລີກ</a>
            </div>
        </div>
    </form>
</main>
<?php include '../../includes/footer.php'; ?>