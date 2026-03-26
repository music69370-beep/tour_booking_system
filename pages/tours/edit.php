<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$id = $_GET['id'];
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = $id"));
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
                <div class="col-md-6">
                    <label class="form-label fw-bold">ເລືອກພາຫະນະ (ລົດທົວ)</label>
                    <select name="vehicle_id" class="form-select" required>
                        <?php 
                        $res_v = mysqli_query($conn, "SELECT * FROM vehicles");
                        while($v = mysqli_fetch_assoc($res_v)) {
                            $selected = ($v['vehicle_id'] == $row['vehicle_id']) ? 'selected' : '';
                            echo "<option value='".$v['vehicle_id']."' $selected>".$v['model']." (".$v['plate_number'].")</option>";
                        }
                        ?>
                    </select>
                </div>
                <!-- ... ສ່ວນທີ່ເຫຼືອ (price, duration, etc.) ໃຫ້ໃສ່ຄືກັບ add.php ແຕ່ໃສ່ value ຂອງ $row ... -->
                <div class="col-12 mt-4">
                    <button type="submit" name="update_tour" class="btn btn-warning px-5 rounded-pill shadow">ອັບເດດຂໍ້ມູນ</button>
                    <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>