<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// 1. ກວດສອບ ແລະ ຮັບ ID ຈາກ URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. ດຶງຂໍ້ມູນທົວທີ່ຕ້ອງການແກ້ໄຂ
$sql = "SELECT * FROM tours WHERE tour_id = '$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// ຖ້າບໍ່ພົບຂໍ້ມູນໃນຖານຂໍ້ມູນ
if (!$row) {
    echo "<div class='container mt-5'><div class='alert alert-danger font-lao text-center'>ບໍ່ພົບຂໍ້ມູນແພັກເກັດທົວນີ້!</div></div>";
    exit;
}
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-edit text-warning me-2"></i>ແກ້ໄຂແພັກເກັດທົວ</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> ຍ້ອນກັບ
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <form action="update.php" method="POST" enctype="multipart/form-data">
                <!-- ສົ່ງ ID ແລະ ຮູບເກົ່າໄປນຳ -->
                <input type="hidden" name="tour_id" value="<?php echo $row['tour_id']; ?>">
                <input type="hidden" name="old_image" value="<?php echo $row['image']; ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ຊື່ແພັກເກັດທົວ</label>
                        <input type="text" name="tour_name" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($row['tour_name']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ເລືອກພາຫະນະ (ລົດທົວ)</label>
                        <select name="vehicle_id" id="vehicle_id" class="form-select bg-light border-0 shadow-none" onchange="updateMaxSeats()" required>
                            <?php 
                            $res_v = mysqli_query($conn, "SELECT * FROM vehicles");
                            while($v = mysqli_fetch_assoc($res_v)) {
                                $selected = ($v['vehicle_id'] == $row['vehicle_id']) ? 'selected' : '';
                                echo "<option value='".$v['vehicle_id']."' data-cap='".$v['capacity']."' $selected>".$v['model']." (".$v['plate_number'].")</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ເລືອກໄກ້ຜູ້ນຳທ່ຽວ</label>
                        <select name="guide_id" class="form-select bg-light border-0 shadow-none" required>
                            <?php 
                            $res_g = mysqli_query($conn, "SELECT * FROM guides");
                            while($g = mysqli_fetch_assoc($res_g)) {
                                $selected_g = ($g['guide_id'] == $row['guide_id']) ? 'selected' : '';
                                echo "<option value='".$g['guide_id']."' $selected_g>".$g['fullname']." (".$g['languages'].")</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small">ລາຄາ (ກີບ)</label>
                        <input type="number" name="price" class="form-control bg-light border-0" value="<?php echo $row['price']; ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small">ໄລຍະເວລາ</label>
                        <input type="text" name="duration" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($row['duration']); ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-primary">ບ່ອນນັ່ງທັງໝົດ</label>
                        <input type="number" name="max_seats" id="max_seats" class="form-control bg-white border-primary fw-bold" value="<?php echo $row['max_seats']; ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small">ຈຳນວນອາຫານ (ຄາບ)</label>
                        <input type="number" name="meals" class="form-control bg-light border-0" value="<?php echo $row['meals']; ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small">ສະຖານະທົວ</label>
                        <select name="status" class="form-select bg-light border-0">
                            <option value="Active" <?php if($row['status']=='Active') echo 'selected'; ?>>Active (ເປີດ)</option>
                            <option value="Inactive" <?php if($row['status']=='Inactive') echo 'selected'; ?>>Inactive (ປິດ)</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-danger">ລາຍລະອຽດແຜນການເດີນທາງ</label>
                        <textarea name="itinerary" class="form-control bg-light border-0" rows="4"><?php echo htmlspecialchars($row['itinerary']); ?></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-success">ກິດຈະກຳຫຼັກ</label>
                        <textarea name="activities" class="form-control bg-light border-0" rows="2"><?php echo htmlspecialchars($row['activities']); ?></textarea>
                    </div>

                    <div class="col-md-6 mt-4">
                        <label class="form-label fw-bold small">ຮູບພາບປະກອບ (ປະໄວ້ຖ້າບໍ່ປ່ຽນ)</label>
                        <input type="file" name="image" class="form-control bg-light border-0 small" accept="image/*">
                        <div class="mt-3">
                            <small class="text-muted d-block mb-2">ຮູບປັດຈຸບັນ:</small>
                            <img src="../../assets/uploads/tours/<?php echo $row['image']; ?>" class="rounded border shadow-sm" width="150">
                        </div>
                    </div>

                    <div class="col-12 mt-5 text-center border-top pt-4">
                        <button type="submit" name="update_tour" class="btn btn-warning px-5 rounded-pill shadow fw-bold">
                            <i class="fas fa-save me-2"></i> ບັນທຶກການປ່ຽນແປງ
                        </button>
                        <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function updateMaxSeats() {
    const select = document.getElementById('vehicle_id');
    const seatsInput = document.getElementById('max_seats');
    const selectedOption = select.options[select.selectedIndex];
    const capacity = selectedOption.getAttribute('data-cap');
    if (capacity) seatsInput.value = capacity;
}
</script>

<?php include '../../includes/footer.php'; ?>