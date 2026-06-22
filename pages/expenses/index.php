<?php 
include '../../config/db.php';
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// Array ສຳລັບແປໝວດໝູ່
$cat_map = [
    'Fuel' => 'ຄ່ານ້ຳມັນ',
    'Hotel' => 'ຄ່າທີ່ພັກ/ໂຮງແຮມ',
    'Maintenance' => 'ຄ່າບຳລຸງຮັກສາລົດ',
    'Food' => 'ຄ່າອາຫານ',
    'Guide_Fee' => 'ຄ່າຈ້າງໄກ້',
    'Entrance_Fee' => 'ຄ່າເຂົ້າຊົມ',
    'Other' => 'ອື່ນໆ'
];
?>
<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 py-4">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h2 class="fw-bold"><i class="fas fa-coins text-danger me-2"></i>ບັນທຶກລາຍຈ່າຍທົວ</h2>
            <button class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addExpenseModal">+ ເພີ່ມລາຍຈ່າຍ</button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">ວັນທີຈ່າຍ</th>
                        <th>ແພັກເກັດທົວ / ຮອບວັນທີ</th>
                        <th>ໝວດໝູ່</th>
                        <th>ລາຍລະອຽດ</th>
                        <th class="text-end">ຈຳນວນເງິນ (ກີບ)</th>
                        <th class="text-center">ຈັດການ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sql = "SELECT e.*, t.tour_name FROM tour_expenses e JOIN tours t ON e.tour_id = t.tour_id ORDER BY e.expense_id DESC";
                    $res = mysqli_query($conn, $sql);
                    if(mysqli_num_rows($res) > 0):
                        while($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td class="ps-4"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <b><?php echo $row['tour_name']; ?></b><br>
                                <small class="text-muted">ຮອບເດີນທາງ: <?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                    <?php echo $cat_map[$row['category']] ?? $row['category']; ?>
                                </span>
                            </td>
                            <td class="small"><?php echo $row['description']; ?></td>
                            <td class="text-end fw-bold text-danger"><?php echo number_format($row['amount']); ?></td>
                            <td class="text-center">
                                <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['expense_id']; ?>, 'delete.php')" class="text-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນລາຍຈ່າຍ</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal ຟອມເພີ່ມລາຍຈ່າຍ -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <form action="save.php" method="POST">
                    <div class="modal-header border-0 bg-danger text-white p-4">
                        <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>ເພີ່ມລາຍຈ່າຍໃໝ່</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">1. ເລືອກແພັກເກັດທົວ</label>
                            <select name="tour_id" class="form-select border-0 bg-light" required>
                                <option value="">-- ເລືອກທົວ --</option>
                                <?php 
                                $tours = mysqli_query($conn, "SELECT tour_id, tour_name FROM tours WHERE status='Active'");
                                while($t = mysqli_fetch_assoc($tours)) echo "<option value='{$t['tour_id']}'>{$t['tour_name']}</option>";
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">2. ວັນທີເດີນທາງຂອງຮອບນີ້ (ເພື່ອໄລ່ກຳໄລ)</label>
                            <input type="date" name="travel_date" class="form-control border-0 bg-light" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">3. ໝວດໝູ່ລາຍຈ່າຍ</label>
                            <select name="category" class="form-select border-0 bg-light" required>
                                <option value="Fuel">⛽ ຄ່ານ້ຳມັນ</option>
                                <option value="Hotel">🏨 ຄ່າທີ່ພັກ/ໂຮງແຮມ</option>
                                <option value="Maintenance">🛠️ ຄ່າບຳລຸງຮັກສາລົດ</option>
                                <option value="Food">🍴 ຄ່າອາຫານ</option>
                                <option value="Guide_Fee">🙋 ຄ່າຈ້າງໄກ້</option>
                                <option value="Entrance_Fee">🎟️ ຄ່າເຂົ້າຊົມ</option>
                                <option value="Other">📦 ອື່ນໆ</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">4. ຈຳນວນເງິນ (ກີບ)</label>
                            <input type="number" name="amount" class="form-control border-0 bg-light fw-bold text-danger fs-4" placeholder="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">5. ລາຍລະອຽດເພີ່ມເຕີມ</label>
                            <textarea name="description" class="form-control border-0 bg-light" rows="2" placeholder="ລະບຸລາຍລະອຽດ..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" name="save_expense" class="btn btn-danger w-100 rounded-pill py-3 fw-bold shadow">
                            <i class="fas fa-save me-2"></i> ບັນທຶກລາຍຈ່າຍ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>