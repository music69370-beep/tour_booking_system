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
        <h2 class="fw-bold"><i class="fas fa-edit me-2 text-warning"></i>ແກ້ໄຂແພັກເກັດທົວ</h2>
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
                <div class="col-md-6">
                    <label class="form-label fw-bold">ເລືອກພາຫະນະ (ລົດທົວ)</label>
                    <select name="vehicle_id" id="vehicle_id" class="form-select" onchange="updateMaxSeats()" required>
                        <?php 
                        $res_v = mysqli_query($conn, "SELECT * FROM vehicles");
                        while($v = mysqli_fetch_assoc($res_v)) {
                            $selected = ($v['vehicle_id'] == $row['vehicle_id']) ? 'selected' : '';
                            echo "<option value='".$v['vehicle_id']."' data-cap='".$v['capacity']."' $selected>".$v['model']." (".$v['plate_number'].")</option>";
                        }
                        ?>
                    </select>
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
                    <label class="form-label fw-bold">ອາຫານ (ຄາບ)</label>
                    <input type="number" name="meals" class="form-control" value="<?php echo $row['meals']; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary">ບ່ອນນັ່ງທັງໝົດ</label>
                    <input type="number" name="max_seats" id="max_seats" class="form-control border-primary fw-bold" value="<?php echo $row['max_seats']; ?>" required>
                </div>

                <!-- ... ສ່ວນ itinerary, activities, image ຄືເກົ່າ ... -->
                
                <div class="col-12 mt-4 text-center border-top pt-4">
                    <img src="../../assets/uploads/tours/<?php echo $row['image']; ?>" class="rounded mb-3 border shadow-sm" width="150">
                    <br>
                    <button type="submit" name="update_tour" class="btn btn-warning px-5 rounded-pill shadow fw-bold">
                        <i class="fas fa-save me-2"></i> ບັນທຶກການປ່ຽນແປງ
                    </button>
                    <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
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