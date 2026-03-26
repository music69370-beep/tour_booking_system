<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$id = $_GET['id'];
$res = mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = $id");
$row = mysqli_fetch_assoc($res);
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold">ແກ້ໄຂແພັກເກັດທົວ</h2>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="update.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="tour_id" value="<?php echo $row['tour_id']; ?>">
            <input type="hidden" name="old_image" value="<?php echo $row['image']; ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">ຊື່ແພັກເກັດທົວ</label>
                    <input type="text" name="tour_name" class="form-control" value="<?php echo $row['tour_name']; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">ລາຄາ (ກີບ)</label>
                    <input type="number" name="price" class="form-control" value="<?php echo $row['price']; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">ໄລຍະເວລາ</label>
                    <input type="text" name="duration" class="form-control" value="<?php echo $row['duration']; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">ຈຳນວນບ່ອນນັ່ງທັງໝົດ</label>
                    <input type="number" name="max_seats" class="form-control" value="<?php echo $row['max_seats']; ?>" min="1" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold">ຮູບພາບ (ປະໄວ້ຖ້າບໍ່ຕ້ອງການປ່ຽນ)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">ສະຖານະ</label>
                    <select name="status" class="form-select">
                        <option value="Active" <?php if($row['status']=='Active') echo 'selected'; ?>>Active (ເປີດໃຫ້ຈອງ)</option>
                        <option value="Inactive" <?php if($row['status']=='Inactive') echo 'selected'; ?>>Inactive (ປິດຊົ່ວຄາວ)</option>
                    </select>
                </div>
                <div class="col-12 mt-4 pt-3 border-top text-center">
                    <img src="../../assets/uploads/tours/<?php echo $row['image']; ?>" class="rounded mb-3 border shadow-sm" width="200">
                    <br>
                    <button type="submit" name="update_tour" class="btn btn-warning px-5 rounded-pill shadow">ບັນທຶກການປ່ຽນແປງ</button>
                    <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>