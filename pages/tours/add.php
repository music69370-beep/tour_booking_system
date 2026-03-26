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
                    <input type="text" name="tour_name" class="form-control shadow-sm" placeholder="ຕົວຢ່າງ: ທ່ຽວຫຼວງພະບາງ" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">ເລືອກພາຫະນະ (ລົດທົວ)</label>
                    <select name="vehicle_id" id="vehicle_id" class="form-select shadow-sm" onchange="updateMaxSeats()" required>
                        <option value="">-- ກະລຸນາເລືອກລົດ --</option>
                        <?php 
                        $res_v = mysqli_query($conn, "SELECT * FROM vehicles WHERE status = 'Available'");
                        while($v = mysqli_fetch_assoc($res_v)) {
                            // ເກັບຄ່າ capacity ໄວ້ໃນ data-cap
                            echo "<option value='".$v['vehicle_id']."' data-cap='".$v['capacity']."'>".$v['model']." (".$v['plate_number'].") - ".$v['capacity']." ບ່ອນນັ່ງ</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">ລາຄາ (ກີບ)</label>
                    <input type="number" name="price" class="form-control shadow-sm" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">ໄລຍະເວລາ</label>
                    <input type="text" name="duration" class="form-control shadow-sm" placeholder="3 ມື້ 2 ຄືນ">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">ຈຳນວນຄາບອາຫານ</label>
                    <input type="number" name="meals" class="form-control shadow-sm" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary">ບ່ອນນັ່ງທັງໝົດ</label>
                    <input type="number" name="max_seats" id="max_seats" class="form-control shadow-sm border-primary fw-bold" value="0" required>
                    <small class="text-muted small">* ປ່ຽນອັດຕະໂນມັດຕາມລົດທີ່ເລືອກ</small>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold">ຮູບພາບປະກອບ</label>
                    <input type="file" name="image" class="form-control shadow-sm" accept="image/*" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold text-danger">ລາຍລະອຽດແຜນການເດີນທາງ</label>
                    <textarea name="itinerary" class="form-control shadow-sm" rows="4" placeholder="ມື້ທີ 1: ...&#10;ມື້ທີ 2: ..."></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold text-success">ກິດຈະກຳຫຼັກ</label>
                    <textarea name="activities" class="form-control shadow-sm" rows="2" placeholder="ລວມກິດຈະກຳຫຍັງແດ່..."></textarea>
                </div>

                <div class="col-12 mt-4 text-end">
                    <button type="submit" name="save_tour" class="btn btn-primary px-5 rounded-pill shadow">
                        <i class="fas fa-save me-2"></i> ບັນທຶກຂໍ້ມູນທົວ
                    </button>
                    <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
// ຟັງຊັນສຳລັບອັບເດດບ່ອນນັ່ງອັດຕະໂນມັດ
function updateMaxSeats() {
    const select = document.getElementById('vehicle_id');
    const seatsInput = document.getElementById('max_seats');
    
    // ດຶງຄ່າຈາກ Attribute data-cap ຂອງ option ທີ່ຖືກເລືອກ
    const selectedOption = select.options[select.selectedIndex];
    const capacity = selectedOption.getAttribute('data-cap');
    
    if (capacity) {
        seatsInput.value = capacity;
    } else {
        seatsInput.value = 0;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>