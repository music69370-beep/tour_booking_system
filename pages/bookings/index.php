<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$tour_filter = isset($_GET['tour_id']) ? $_GET['tour_id'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-calendar-check text-primary me-2"></i>ລາຍການຈອງທົວ</h2>
            <div class="d-flex gap-2">
                <a href="export.php" class="btn btn-success rounded-pill px-3 shadow-sm small"><i class="fas fa-file-excel me-1"></i> Excel</a>
                <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm small">+ ສ້າງການຈອງໃໝ່</a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
            <form action="" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">ກັ່ນຕອງຕາມແພັກເກັດ:</label>
                    <select name="tour_id" class="form-select form-select-sm border-0 bg-light" onchange="this.form.submit()">
                        <option value="all">-- ທຸກແພັກເກັດ --</option>
                        <?php 
                        $t_list = mysqli_query($conn, "SELECT tour_id, tour_name FROM tours");
                        while($t = mysqli_fetch_assoc($t_list)){
                            $sel = ($tour_filter == $t['tour_id']) ? 'selected' : '';
                            echo "<option value='{$t['tour_id']}' $sel>{$t['tour_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">ສະຖານະ:</label>
                    <select name="status" class="form-select form-select-sm border-0 bg-light" onchange="this.form.submit()">
                        <option value="all" <?php echo ($status_filter=='all')?'selected':''; ?>>ທັງໝົດ</option>
                        <option value="Pending" <?php echo ($status_filter=='Pending')?'selected':''; ?>>ລໍຖ້າອະນຸມັດ</option>
                        <option value="Confirmed" <?php echo ($status_filter=='Confirmed')?'selected':''; ?>>ອະນຸມັດແລ້ວ</option>
                        <option value="Cancelled" <?php echo ($status_filter=='Cancelled')?'selected':''; ?>>ຍົກເລີກແລ້ວ</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">ຄົ້ນຫາ:</label>
                    <input type="text" name="search" class="form-control form-control-sm border-0 bg-light" placeholder="ຊື່ລູກຄ້າ..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-sm w-100 rounded-pill" type="submit"><i class="fas fa-search me-1"></i> ຄົ້ນຫາ</button>
                </div>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ວັນທີເດີນທາງ</th>
                            <th>ລູກຄ້າ</th>
                            <th>ແພັກເກັດທົວ</th>
                            <th class="text-end">ລາຄາລວມ</th>
                            <th class="text-center">ລາຍການວຽກ</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT b.*, c.fullname, c.phone, t.tour_name,
                                (SELECT payment_slip FROM payments WHERE booking_id = b.booking_id ORDER BY payment_id DESC LIMIT 1) as slip
                                FROM bookings b
                                JOIN customers c ON b.customer_id = c.customer_id
                                JOIN tours t ON b.tour_id = t.tour_id";
                        
                        $where = [];
                        if ($status_filter != 'all') $where[] = "b.status = '$status_filter'";
                        if ($tour_filter != 'all') $where[] = "b.tour_id = '$tour_filter'";
                        if ($search != '') $where[] = "(c.fullname LIKE '%$search%' OR t.tour_name LIKE '%$search%')";
                        
                        if (count($where) > 0) $sql .= " WHERE " . implode(' AND ', $where);
                        $sql .= " ORDER BY b.travel_date ASC";
                        
                        $result = mysqli_query($conn, $sql);
                        if($result && mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                                $bid = $row['booking_id'];
                                $st = $row['status'];
                                $slip_url = "../../assets/uploads/payments/" . $row['slip'];
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-primary"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></div>
                                    <small class="text-muted" style="font-size: 0.7rem;">ID: #BK-<?php echo $bid; ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $row['fullname']; ?></div>
                                    <small class="text-muted"><?php echo $row['phone']; ?></small>
                                </td>
                                <td>  
                                    <div class="small fw-bold text-dark"><?php echo $row['tour_name']; ?></div>
                                    <span class="badge bg-light text-dark border small" style="font-size: 0.65rem;">Room: <?php echo $row['room_type']; ?></span>
                                </td>
                                <td class="text-end fw-bold text-danger"><?php echo number_format($row['total_price']); ?></td>
                                <td class="text-center">
                                    <a href="view.php?id=<?php echo $bid; ?>#task-section" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                        <i class="fas fa-tasks me-1"></i> ກວດວຽກ
                                    </a>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    if ($st == 'Confirmed') echo '<span class="badge rounded-pill bg-success px-2 py-1 small">ອະນຸມັດແລ້ວ</span>';
                                    elseif ($st == 'Cancelled') echo '<span class="badge rounded-pill bg-danger px-2 py-1 small">ຍົກເລີກແລ້ວ</span>';
                                    else echo '<span class="badge rounded-pill bg-warning text-dark px-2 py-1 small">ລໍຖ້າອະນຸມັດ</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group border rounded-pill overflow-hidden shadow-sm bg-white">
                                        
                                        <!-- ປຸ່ມເບິ່ງສະລິບ (ປ່ຽນມາໃຊ້ຟັງຊັນ JavaScript Pop-up) -->
                                        <?php if(!empty($row['slip'])): ?>
                                            <button type="button" onclick="showSlip('<?php echo $slip_url; ?>')" class="btn btn-sm btn-white text-primary border-end" title="ເບິ່ງສະລິບ">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-white text-muted border-end" disabled title="ບໍ່ມີສະລິບ">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </button>
                                        <?php endif; ?>

                                        <?php if($st == 'Pending' && isAdmin()): ?> 
                                            <a href="javascript:void(0)" onclick="confirmApprove(<?php echo $bid; ?>, 'approve.php')" class="btn btn-sm btn-white text-success border-end" title="ອະນຸມັດ">
                                                <i class="fas fa-check-circle"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if($st != 'Cancelled' && isAdmin()): ?>
                                            <a href="cancel_form.php?id=<?php echo $bid; ?>" class="btn btn-sm btn-white text-secondary border-end" title="ຍົກເລີກ">
                                                <i class="fas fa-times-circle"></i>
                                            </a>
                                        <?php endif; ?>

                                        <a href="view.php?id=<?php echo $bid; ?>" class="btn btn-sm btn-white text-primary border-end" title="ເບິ່ງ"><i class="fas fa-eye"></i></a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $bid; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger" title="ລຶບ"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">ບໍ່ມີຂໍ້ມູນ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal ສຳລັບໂຊຮູບສະລິບ (Pop-up) -->
<div class="modal fade" id="slipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-5 border-0 shadow-lg">
            <div class="modal-header border-0 bg-primary text-white p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i>ຫຼັກຖານການໂອນເງິນ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2 text-center bg-light">
                <img id="slipImage" src="" class="img-fluid rounded-4 shadow-sm border" style="max-height: 75vh;">
            </div>
            <div class="modal-footer border-0 bg-light justify-content-center p-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ປິດໜ້າຕ່າງ</button>
                <a id="downloadSlip" href="" download class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-download me-1"></i> ດາວໂຫລດຮູບ
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// ຟັງຊັນເປີດ Pop-up ແລະ ເອົາຮູບໄປໃສ່
function showSlip(url) {
    document.getElementById('slipImage').src = url;
    document.getElementById('downloadSlip').href = url;
    var myModal = new bootstrap.Modal(document.getElementById('slipModal'));
    myModal.show();
}
</script>

<style>
    .btn-white { background: #fff; border: none; padding: 5px 10px; }
    .btn-white:hover { background: #f8f9fa; }
    .btn-group .btn { font-size: 0.85rem; }
    .modal-content { overflow: hidden; }
</style>

<?php include '../../includes/footer.php'; ?>