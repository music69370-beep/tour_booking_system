<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$id'"));
if (!$row) { exit; }
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
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
                    <div class="col-md-4"><label class="form-label fw-bold small">ລະຫັດແພັກເກັດ</label><input type="text" name="tour_code" class="form-control bg-light border-0" value="<?php echo $row['tour_code']; ?>" required></div>
                    <div class="col-md-8"><label class="form-label fw-bold small">ຊື່ແພັກເກັດທົວ</label><input type="text" name="tour_name" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($row['tour_name']); ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-bold small">ລາຄາຂາຍ/ທ່ານ</label><input type="number" name="price" class="form-control border-primary" value="<?php echo $row['price']; ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-bold small">ບ່ອນນັ່ງສູງສຸດ</label><input type="number" name="max_seats" class="form-control bg-light border-0" value="<?php echo $row['max_seats']; ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-bold small">ວັນທີເລີ່ມ</label><input type="date" name="start_date" class="form-control bg-light border-0" value="<?php echo $row['start_date']; ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-bold small">ວັນທີສິ້ນສຸດ</label><input type="date" name="end_date" class="form-control bg-light border-0" value="<?php echo $row['end_date']; ?>" required></div>
                    <div class="col-md-12"><label class="form-label fw-bold small">ແຜນການເດີນທາງ</label><textarea name="itinerary" class="form-control bg-light border-0" rows="5"><?php echo $row['itinerary']; ?></textarea></div>
                    <div class="col-md-12"><label class="form-label fw-bold small">ຮູບພາບ</label><input type="file" name="image" class="form-control bg-light border-0 small"><img src="../../assets/uploads/tours/<?php echo $row['image']; ?>" class="rounded mt-2" width="100"></div>
                </div>
                <div class="col-12 text-center mt-5"><button type="submit" name="update_tour" class="btn btn-warning px-5 rounded-pill shadow fw-bold">ບັນທຶກການປ່ຽນແປງ</button></div>
            </form>
        </div>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>