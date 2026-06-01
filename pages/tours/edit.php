<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) { echo "<script>window.location='index.php';</script>"; exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$id'"));
if (!$row) { echo "<div class='container mt-5'><div class='alert alert-danger font-lao text-center'>ບໍ່ພົບຂໍ້ມູນ!</div></div>"; exit; }
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-edit text-warning me-2"></i>ແກ້ໄຂແພັກເກັດທົວ</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍ້ອນກັບ</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <form action="update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tour_id" value="<?php echo $row['tour_id']; ?>">
                <input type="hidden" name="old_image" value="<?php echo $row['image']; ?>">

                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-bold small">ຊື່ແພັກເກັດທົວ</label><input type="text" name="tour_name" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($row['tour_name']); ?>" required></div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ເລືອກພາຫະນະ</label>
                        <select name="vehicle_id" class="form-select bg-light border-0">
                            <?php $res_v = mysqli_query($conn, "SELECT * FROM vehicles"); while($v = mysqli_fetch_assoc($res_v)) { $s = ($v['vehicle_id'] == $row['vehicle_id']) ? 'selected' : ''; echo "<option value='".$v['vehicle_id']."' $s>".$v['model']." (".$v['plate_number'].")</option>"; } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ເລືອກໄກ້</label>
                        <select name="guide_id" class="form-select bg-light border-0">
                            <?php $res_g = mysqli_query($conn, "SELECT * FROM guides"); while($g = mysqli_fetch_assoc($res_g)) { $s = ($g['guide_id'] == $row['guide_id']) ? 'selected' : ''; echo "<option value='".$g['guide_id']."' $s>".$g['fullname']."</option>"; } ?>
                        </select>
                    </div>
                    <div class="col-md-3"><label class="form-label fw-bold small">ລາຄາຂາຍ (ກີບ)</label><input type="number" name="price" class="form-control bg-light border-0" value="<?php echo $row['price']; ?>" required></div>
                    <!-- ເອົາສ່ວນຕົ້ນທຶນອອກແລ້ວ -->
                    <div class="col-md-3"><label class="form-label fw-bold small">ໄລຍະເວລາ</label><input type="text" name="duration" class="form-control bg-light border-0" value="<?php echo $row['duration']; ?>"></div>
                    <div class="col-md-3"><label class="form-label fw-bold small text-primary">ບ່ອນນັ່ງທັງໝົດ</label><input type="number" name="max_seats" class="form-control bg-white border-primary fw-bold" value="<?php echo $row['max_seats']; ?>" required></div>
                    <div class="col-md-3"><label class="form-label fw-bold small">ສະຖານະ</label><select name="status" class="form-select bg-light border-0"><option value="Active" <?php if($row['status']=='Active') echo 'selected'; ?>>Active</option><option value="Inactive" <?php if($row['status']=='Inactive') echo 'selected'; ?>>Inactive</option></select></div>
                    <div class="col-md-12"><label class="form-label fw-bold small text-danger">ແຜນການເດີນທາງ</label><textarea name="itinerary" class="form-control bg-light border-0" rows="4"><?php echo $row['itinerary']; ?></textarea></div>
                    <div class="col-md-6 mt-4"><label class="form-label fw-bold small">ຮູບພາບປະກອບ</label><input type="file" name="image" class="form-control bg-light border-0 small" accept="image/*"><img src="../../assets/uploads/tours/<?php echo $row['image']; ?>" class="rounded border mt-2" width="120"></div>
                </div>
                <div class="col-12 mt-5 text-center border-top pt-4"><button type="submit" name="update_tour" class="btn btn-warning px-5 rounded-pill shadow fw-bold">ບັນທຶກການປ່ຽນແປງ</button></div>
            </form>
        </div>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>