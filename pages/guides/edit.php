<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$id = mysqli_real_escape_string($conn, $_GET['id']);
$res = mysqli_query($conn, "SELECT * FROM guides WHERE guide_id = '$id'");
$row = mysqli_fetch_assoc($res);
if (!$row) exit("Data not found");
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 pb-5">
        <h2 class="fw-bold text-dark pt-3 pb-2 mb-3 border-bottom">ແກ້ໄຂຂໍ້ມູນໄກ້</h2>
        <form action="update.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="guide_id" value="<?php echo $row['guide_id']; ?>">
            <input type="hidden" name="old_image" value="<?php echo $row['image']; ?>">
            <input type="hidden" name="old_doc" value="<?php echo $row['doc_attachment']; ?>">

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-3 text-primary">ຂໍ້ມູນສ່ວນຕົວ & ວິຊາຊີບ</h5>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ຊື່ ແລະ ນາມສະກຸນ</label>
                            <input type="text" name="fullname" class="form-control bg-light border-0" value="<?php echo $row['fullname']; ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">ເລກບັດໄກ້</label>
                                <input type="text" name="license_id" class="form-control bg-light border-0" value="<?php echo $row['license_id']; ?>" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">ວັນໝົດອາຍຸບັດ</label>
                                <input type="date" name="license_expiry" class="form-control bg-light border-0" value="<?php echo $row['license_expiry']; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ພາສາ</label>
                            <input type="text" name="languages" class="form-control bg-light border-0" value="<?php echo $row['languages']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ສະຖານະ</label>
                            <select name="status" class="form-select bg-light border-0">
                                <option value="Available" <?php echo ($row['status']=='Available')?'selected':''; ?>>Available (ຫວ່າງ)</option>
                                <option value="Busy" <?php echo ($row['status']=='Busy')?'selected':''; ?>>Busy (ຕິດວຽກ)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-3 text-success">ຂໍ້ມູນການຕິດຕໍ່ & ການເງິນ</h5>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ເບີໂທລະສັບ</label>
                            <input type="text" name="phone" class="form-control bg-light border-0" value="<?php echo $row['phone']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ເລືອກທະນາຄານ</label>
                            <select name="bank_name" class="form-select bg-light border-0">
                                <option value="BCEL" <?php echo ($row['bank_name']=='BCEL')?'selected':''; ?>>BCEL</option>
                                <option value="LDB" <?php echo ($row['bank_name']=='LDB')?'selected':''; ?>>LDB</option>
                                <option value="JDB" <?php echo ($row['bank_name']=='JDB')?'selected':''; ?>>JDB</option>
                                <!-- ທ່ານສາມາດເພີ່ມລາຍຊື່ທະນາຄານອື່ນໄດ້ຕາມ add.php -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ເລກບັນຊີ</label>
                            <input type="text" name="bank_account" class="form-control bg-light border-0" value="<?php echo $row['bank_account']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ຮູບພາບປະຈຳຕົວ</label>
                            <input type="file" name="image" class="form-control bg-light border-0" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center">
                    <button type="submit" name="update_guide" class="btn btn-warning px-5 rounded-pill shadow fw-bold">ບັນທຶກການແກ້ໄຂ</button>
                    <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>