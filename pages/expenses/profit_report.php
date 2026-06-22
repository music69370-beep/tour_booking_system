<?php 
include '../../config/db.php';
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>
<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 py-4">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h2 class="fw-bold text-dark"><i class="fas fa-chart-pie text-success me-2"></i>ລາຍງານກຳໄລແຍກຕາມຮອບທົວ</h2>
            <div>
                <button onclick="location.reload()" class="btn btn-light border rounded-pill px-3 small me-2">
                    <i class="fas fa-sync-alt"></i> ໂຫຼດຂໍ້ມູນໃໝ່
                </button>
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-3 small">
                    <i class="fas fa-print me-1"></i> ພິມລາຍງານ
                </button>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="bg-primary text-white small">
                        <tr>
                            <th class="py-3">ວັນທີເດີນທາງ</th>
                            <th class="text-start">ຊື່ແພັກເກັດທົວ</th>
                            <th class="text-end">ລາຍຮັບ (Confirmed)</th>
                            <th class="text-end">ລາຍຈ່າຍຕົວຈິງ</th>
                            <th class="text-end">ກຳໄລສຸດທິ</th>
                            <th>ໝາຍເຫດ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Query ໃໝ່: ດຶງຂໍ້ມູນມາໂຊກ່ອນ ເຖິງວ່າຈະຍັງບໍ່ໄດ້ອະນຸມັດ ຫຼື ຍັງບໍ່ມີລາຍຈ່າຍກໍຕາມ
                        $sql = "SELECT 
                                    t.tour_name, 
                                    b.tour_id, 
                                    b.travel_date, 
                                    SUM(CASE WHEN b.status = 'Confirmed' THEN b.total_price ELSE 0 END) as total_income,
                                    (SELECT SUM(amount) FROM tour_expenses WHERE tour_id = b.tour_id AND travel_date = b.travel_date) as total_expense,
                                    COUNT(b.booking_id) as total_bookings
                                FROM bookings b
                                JOIN tours t ON b.tour_id = t.tour_id
                                WHERE b.status != 'Cancelled'
                                GROUP BY b.tour_id, b.travel_date
                                ORDER BY b.travel_date DESC";
                        
                        $res = mysqli_query($conn, $sql);
                        
                        if($res && mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)): 
                                $income = (float)$row['total_income'];
                                $expense = (float)($row['total_expense'] ?? 0);
                                $profit = $income - $expense;
                                $bookings = $row['total_bookings'];
                        ?>
                        <tr>
                            <td class="fw-bold text-muted"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></td>
                            <td class="text-start">
                                <div class="fw-bold text-dark"><?php echo $row['tour_name']; ?></div>
                                <small class="badge bg-light text-muted border"><?php echo $bookings; ?> ການຈອງ</small>
                            </td>
                            <td class="text-end text-primary fw-bold"><?php echo number_format($income); ?></td>
                            <td class="text-end text-danger fw-bold"><?php echo number_format($expense); ?></td>
                            <td class="text-end fw-bold fs-5 <?php echo ($profit >= 0) ? 'text-success' : 'text-danger'; ?>">
                                <?php echo ($profit >= 0 ? '+ ' : '') . number_format($profit); ?>
                            </td>
                            <td>
                                <?php if($income == 0 && $bookings > 0): ?>
                                    <span class="text-warning small"><i class="fas fa-exclamation-circle"></i> ລໍຖ້າອະນຸມັດການຈອງ</span>
                                <?php elseif($profit >= 0): ?>
                                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3">ກຳໄລ</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3">ຂາດທຶນ</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="6" class="py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-20"></i><br>
                                ບໍ່ມີຂໍ້ມູນການຈອງ ຫຼື ລາຍຈ່າຍໃນລະບົບ
                            </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="alert alert-info border-0 shadow-sm rounded-4 small">
                    <h6 class="fw-bold"><i class="fas fa-info-circle me-1"></i> ວິທີໃຫ້ຂໍ້ມູນສະແດງຜົນ:</h6>
                    <ol class="mb-0 ps-3">
                        <li>ຕ້ອງມີການຈອງລູກຄ້າໃນແພັກເກັດນັ້ນ.</li>
                        <li>ໄປທີ່ໜ້າ <b>"ລາຍການຈອງທັງໝົດ"</b> ແລ້ວກົດ <b>"ອະນຸມັດ"</b> ເພື່ອໃຫ້ມີລາຍຮັບ.</li>
                        <li>ໄປທີ່ໜ້າ <b>"ບັນທຶກລາຍຈ່າຍ"</b> ແລ້ວເລືອກວັນທີເດີນທາງໃຫ້ <b>"ຕົງກັນ"</b> ກັບມື້ທີ່ລູກຄ້າຈອງ.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>