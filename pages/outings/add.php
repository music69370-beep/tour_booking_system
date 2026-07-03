<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-bus-alt text-info me-2"></i>ປ່ອຍລົດອອກທົວ (ຈັດກຸ່ມລົດ)</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍ້ອນກັບ</a>
        </div>

        <form action="save.php" method="POST" id="outingForm">
            <div class="row g-4">
                <!-- ສ່ວນທີ 1: ເລືອກທົວ ແລະ ວັນທີ -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-3 text-primary">1. ຂໍ້ມູນແພັກເກັດທົວ</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ເລືອກແພັກເກັດທົວ</label>
                                <select name="tour_id" id="tour_select" class="form-select bg-light border-0 py-2" onchange="syncDates()" required>
                                    <option value="">-- ເລືອກທົວທີ່ກຳລັງຈະເດີນທາງ --</option>
                                    <?php 
                                    $t_res = mysqli_query($conn, "SELECT * FROM tours WHERE status='Active' ORDER BY start_date ASC");
                                    while($t = mysqli_fetch_assoc($t_res)) {
                                        echo "<option value='{$t['tour_id']}' data-start='{$t['start_date']}' data-end='{$t['end_date']}'>{$t['tour_name']} (".date('d/m/Y', strtotime($t['start_date'])).")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">ວັນທີອອກ</label>
                                <input type="date" name="start_date" id="start_date" class="form-control bg-light border-0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">ວັນທີກັບ</label>
                                <input type="date" name="return_date" id="return_date" class="form-control bg-light border-0" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ສ່ວນທີ 2: ຈັດການລົດ ແລະ ຄົນຂັບ (Dynamic Rows) -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-success mb-0">2. ລາຍການລົດ ແລະ ຄົນຂັບ ທີ່ອອກໄປນຳກັນ</h5>
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="addVehicleRow()">
                                <i class="fas fa-plus-circle me-1"></i> + ເພີ່ມລົດອີກຄັນ
                            </button>
                        </div>

                        <div id="vehicle-container">
                            <!-- ບ່ອນວາງລາຍການລົດ -->
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-4">
                    <button type="submit" name="btn_save" class="btn btn-primary btn-lg px-5 rounded-pill shadow fw-bold">
                        <i class="fas fa-save me-2"></i> ຢືນຢັນການປ່ອຍລົດທັງໝົດ
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<!-- HTML Template ສໍາລັບແຖວລົດ -->
<script>
let rowIdx = 0;

function addVehicleRow() {
    rowIdx++;
    const html = `
        <div class="vehicle-row p-3 mb-3 border rounded-4 bg-light shadow-sm" id="row-${rowIdx}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="small fw-bold">ເລືອກລົດ (ສະເພາະຄົນວ່າງ)</label>
                    <select name="vehicle_ids[]" class="form-select border-0" required>
                        <option value="">-- ເລືອກລົດ --</option>
                        <?php 
                        $v_res = mysqli_query($conn, "SELECT * FROM vehicles WHERE status='Available'");
                        while($v = mysqli_fetch_assoc($v_res)) {
                            echo "<option value='{$v['vehicle_id']}'>{$v['plate_number']} - {$v['model']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">ເລືອກຄົນຂັບ (ສະເພາະຄົນວ່າງ)</label>
                    <select name="driver_ids[]" class="form-select border-0" required>
                        <option value="">-- ເລືອກຄົນຂັບ --</option>
                        <?php 
                        $d_res = mysqli_query($conn, "SELECT * FROM drivers WHERE status='Available'");
                        while($d = mysqli_fetch_assoc($d_res)) {
                            echo "<option value='{$d['driver_id']}'>{$d['fullname']} ({$d['phone']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">ເລກໄມເລີ່ມຕົ້ນ</label>
                    <input type="number" name="start_mileages[]" class="form-control border-0" placeholder="0" required>
                </div>
                <div class="col-md-1 text-end">
                    ${rowIdx > 1 ? `<button type="button" class="btn btn-danger btn-sm rounded-circle" onclick="removeRow(${rowIdx})"><i class="fas fa-times"></i></button>` : ''}
                </div>
            </div>
        </div>
    `;
    $('#vehicle-container').append(html);
}

function removeRow(id) {
    $(`#row-${id}`).remove();
}

function syncDates() {
    const sel = document.getElementById('tour_select');
    const opt = sel.options[sel.selectedIndex];
    if(opt.value) {
        document.getElementById('start_date').value = opt.getAttribute('data-start');
        document.getElementById('return_date').value = opt.getAttribute('data-end');
    }
}

function removeRow(id) {
    $(`#row-${id}`).remove();
}

// ເລີ່ມຕົ້ນໃຫ້ມີ 1 ແຖວກ່ອນ
$(document).ready(function() {
    addVehicleRow();
});
</script>

<style>
    .vehicle-row { transition: 0.3s; }
    .vehicle-row:hover { border-color: #198754 !important; }
</style>

<?php include '../../includes/footer.php'; ?>