<?php 
include '../../config/db.php';
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$cat_map = [
    'Fuel'         => '⛽ ຄ່ານ້ຳມັນ',
    'Hotel'        => '🏨 ຄ່າທີ່ພັກ/ໂຮງແຮມ',
    'Maintenance'  => '🔧 ຄ່າບຳລຸງຮັກສາລົດ',
    'Food'         => '🍴 ຄ່າອາຫານ',
    'Guide_Fee'    => '👤 ຄ່າຈ້າງໄກ້',
    'Entrance_Fee' => '🎟️ ຄ່າເຂົ້າຊົມ',
    'Other'        => '⚙️ ອື່ນໆ'
];
?>
<style>
    .modal-content-custom { border: none; border-radius: 25px; box-shadow: 0 15px 50px rgba(0,0,0,0.1); overflow: hidden; }
    .modal-header-custom { background: #ffffff; border-bottom: 1px solid #f1f3f7; padding: 25px 30px; }
    .modal-title-custom { font-weight: 700; color: #2d3436; display: flex; align-items: center; gap: 12px; }
    .form-group-custom label { font-size: 0.85rem; font-weight: 700; color: #636e72; margin-bottom: 8px; display: block; text-transform: uppercase; }
    .input-custom { background-color: #f8f9fc !important; border: 2px solid #f1f3f7 !important; border-radius: 12px !important; padding: 12px 15px !important; transition: all 0.3s ease; }
    .input-custom:focus { border-color: #0d6efd !important; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1) !important; background-color: #fff !important; }
    .btn-save-custom { background: #0d6efd; border: none; padding: 14px 30px; border-radius: 15px; font-weight: 700; box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2); transition: all 0.3s; }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 py-4">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h2 class="fw-bold"><i class="fas fa-coins text-danger me-2"></i>ບັນທຶກລາຍຈ່າຍທົວ</h2>
            <button class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addExpenseModal">+ ເພີ່ມລາຍຈ່າຍ</button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ວັນທີຈ່າຍ</th>
                            <th>ແພັກເກັດທົວ / ຮອບວັນທີ</th>
                            <th>ໝວດໝູ່</th>
                            <th>ຜູ້ບັນທຶກ</th>
                            <th class="text-end">ຈຳນວນເງິນ (ກີບ)</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // ປັບ SQL ໃຫ້ JOIN ເອົາຊື່ພະນັກງານ
                        $sql = "SELECT e.*, t.tour_name, u.fullname as staff_name 
                                FROM tour_expenses e 
                                JOIN tours t ON e.tour_id = t.tour_id 
                                LEFT JOIN users u ON e.created_by = u.user_id
                                ORDER BY e.expense_id DESC";
                        $res = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td class="ps-4"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <b><?php echo $row['tour_name']; ?></b><br>
                                    <small class="text-muted">ຮອບ: <?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></small>
                                </td>
                                <td><span class="badge bg-secondary px-3 py-2 rounded-pill"><?php echo $cat_map[$row['category']] ?? $row['category']; ?></span></td>
                                <td><small class="fw-bold text-primary"><?php echo $row['staff_name'] ?? 'System'; ?></small></td>
                                <td class="text-end fw-bold text-danger"><?php echo number_format($row['amount']); ?></td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['expense_id']; ?>, 'delete.php')" class="text-danger"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນລາຍຈ່າຍ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title modal-title-custom"><i class="fas fa-plus-circle text-primary"></i> ບັນທຶກລາຍຈ່າຍໃໝ່</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="save.php" method="POST">
                    <div class="modal-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-12 form-group-custom">
                                <label>ແພັກເກັດທົວ</label>
                                <select name="tour_id" id="tour_select" class="form-select input-custom" onchange="updateTravelDate()" required>
                                    <option value="">-- ເລືອກແພັກເກັດ --</option>
                                    <?php 
                                        $tours = mysqli_query($conn, "SELECT tour_id, tour_name, start_date FROM tours");
                                        while($t = mysqli_fetch_assoc($tours)) echo "<option value='{$t['tour_id']}' data-date='{$t['start_date']}'>{$t['tour_name']}</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 form-group-custom">
                                <label>ວັນທີເດີນທາງ</label>
                                <input type="date" name="travel_date" id="expense_travel_date" class="form-control input-custom" required>
                            </div>
                            <div class="col-12 form-group-custom">
                                <label>ໝວດໝູ່</label>
                                <select name="category" class="form-select input-custom" required>
                                    <option value="Fuel">⛽ ຄ່ານ້ຳມັນ</option>
                                    <option value="Hotel">🏨 ຄ່າໂຮງແຮມ</option>
                                    <option value="Food">🍴 ຄ່າອາຫານ</option>
                                    <option value="Guide_Fee">👤 ຄ່າໄກ້/ຄົນຂັບ</option>
                                    <option value="Other">⚙️ ອື່ນໆ</option>
                                </select>
                            </div>
                            <div class="col-12 form-group-custom">
                                <label>ຈຳນວນເງິນ (ກີບ)</label>
                                <input type="number" name="amount" class="form-control input-custom fw-bold text-danger" required>
                            </div>
                            <div class="col-12 form-group-custom">
                                <label>ລາຍລະອຽດ</label>
                                <textarea name="note" class="form-control input-custom" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" name="btn_save" class="btn btn-primary btn-save-custom w-100 shadow">ບັນທຶກລາຍຈ່າຍ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<script>
function updateTravelDate() {
    const sel = document.getElementById('tour_select');
    const date = sel.options[sel.selectedIndex].getAttribute('data-date');
    document.getElementById('expense_travel_date').value = date || "";
}
</script>
<?php include '../../includes/footer.php'; ?>