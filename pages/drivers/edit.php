<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

if (!isset($_GET['id'])) { header("Location: index.php"); exit(); }

$id = mysqli_real_escape_string($conn, $_GET['id']);
$res = mysqli_query($conn, "SELECT * FROM drivers WHERE driver_id = '$id'");
$row = mysqli_fetch_assoc($res);

if (!$row) { echo "ບໍ່ພົບຂໍ້ມູນ"; exit(); }
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-user-edit text-warning me-2"></i>ແກ້ໄຂຂໍ້ມູນຄົນຂັບ</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍົກເລີກ</a>
        </div>

        <form action="update.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="driver_id" value="<?php echo $row['driver_id']; ?>">
            <input type="hidden" name="old_image" value="<?php echo $row['image']; ?>">
            <input type="hidden" name="old_license_image" value="<?php echo $row['license_image']; ?>">
            <input type="hidden" name="old_id_card_image" value="<?php echo $row['id_card_image']; ?>">

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-primary">1. ຂໍ້ມູນສ່ວນຕົວ</h5>
                        <div class="row g-3">
                            <div class="col-md-12"><label class="form-label small fw-bold">ຊື່ ແລະ ນາມສະກຸນ</label><input type="text" name="fullname" class="form-control bg-light border-0" value="<?php echo $row['fullname']; ?>" required></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">ເບີໂທລະສັບ</label><input type="text" name="phone" class="form-control bg-light border-0" value="<?php echo $row['phone']; ?>" required></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">ເລກບັດປະຈຳຕົວ</label><input type="text" name="id_card_no" class="form-control bg-light border-0" value="<?php echo $row['id_card_no']; ?>"></div>
                            <div class="col-md-12"><label class="form-label small fw-bold">ທີ່ຢູ່ປະຈຸບັນ</label><textarea name="address" class="form-control bg-light border-0" rows="2"><?php echo $row['address']; ?></textarea></div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">ສະຖານະ</label>
                                <select name="status" class="form-select bg-light border-0">
                                    <option value="Available" <?php if($row['status'] == 'Available') echo 'selected'; ?>>ວ່າງ (Available)</option>
                                    <option value="Busy" <?php if($row['status'] == 'Busy') echo 'selected'; ?>>ຕິດວຽກ (Busy)</option>
                                    <option value="Off" <?php if($row['status'] == 'Off') echo 'selected'; ?>>ພັກຜ່ອນ (Off)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-success">2. ຂໍ້ມູນວິຊາຊີບ & ເອກະສານ</h5>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label small fw-bold">ເລກທີໃບຂັບຂີ່</label><input type="text" name="license_number" class="form-control bg-light border-0" value="<?php echo $row['license_number']; ?>" required></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">ປະເພດໃບຂັບຂີ່</label><select name="license_type" class="form-select bg-light border-0"><option value="B" <?php if($row['license_type'] == 'B') echo 'selected'; ?>>ປະເພດ ຂ</option><option value="C" <?php if($row['license_type'] == 'C') echo 'selected'; ?>>ປະເພດ ຄ</option><option value="D" <?php if($row['license_type'] == 'D') echo 'selected'; ?>>ປະເພດ ງ</option></select></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">ວັນໝົດອາຍຸໃບຂັບຂີ່</label><input type="date" name="license_expiry" class="form-control border-warning" value="<?php echo $row['license_expiry']; ?>" required></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">ປະສົບການ (ປີ)</label><input type="number" name="experience_years" class="form-control bg-light border-0" value="<?php echo $row['experience_years']; ?>"></div>
                            
                            <!-- ຈັດການ 3 ຮູບ -->
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-primary">ຮູບຄົນຂັບ</label>
                                <input type="file" name="image" class="form-control bg-light border-0 small">
                                <?php if($row['image']): ?><img src="../../assets/uploads/drivers/<?php echo $row['image']; ?>" class="mt-2 rounded border" width="60"><?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-primary">ຮູບໃບຂັບຂີ່</label>
                                <input type="file" name="license_image" class="form-control bg-light border-0 small">
                                <?php if($row['license_image']): ?><img src="../../assets/uploads/drivers/<?php echo $row['license_image']; ?>" class="mt-2 rounded border" width="60"><?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-primary">ຮູບບັດປະຈຳຕົວ</label>
                                <input type="file" name="id_card_image" class="form-control bg-light border-0 small">
                                <?php if($row['id_card_image']): ?><img src="../../assets/uploads/drivers/<?php echo $row['id_card_image']; ?>" class="mt-2 rounded border" width="60"><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-5"><button type="submit" name="btn_update" class="btn btn-warning btn-lg px-5 rounded-pill shadow fw-bold">ບັນທຶກການແກ້ໄຂ</button></div>
            </div>
        </form>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>