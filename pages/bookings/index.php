<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// 1. ຮັບຄ່າການກັ່ນຕອງ ແລະ ການຄົ້ນຫາ
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold"><i class="fas fa-calendar-check text-primary me-2"></i>ລາຍການຈອງທົວ</h2>
            <div class="d-flex gap-2">
                <a href="export.php" class="btn btn-success rounded-pill px-3 shadow-sm"><i class="fas fa-file-excel me-1"></i> ສົ່ງອອກ Excel</a>
                <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="fas fa-plus-circle me-1"></i> ສ້າງການຈອງໃໝ່</a>
            </div>
        </div>

        <!-- 2. ສ່ວນປຸ່ມກັ່ນຕອງ (Filter Buttons) -->
        <div class="row mb-4 g-3">
            <div class="col-md-8">
                <div class="btn-group p-1 bg-white shadow-sm rounded-pill">
                    <a href="index.php?status=all" class="btn rounded-pill px-4 <?php echo ($status_filter == 'all') ? 'btn-primary' : 'btn-light'; ?>">ທັງໝົດ</a>
                    <a href="index.php?status=Pending" class="btn rounded-pill px-4 <?php echo ($status_filter == 'Pending') ? 'btn-warning text-dark' : 'btn-light'; ?>">ລໍຖ້າອະນຸມັດ</a>
                    <a href="index.php?status=Confirmed" class="btn rounded-pill px-4 <?php echo ($status_filter == 'Confirmed') ? 'btn-success' : 'btn-light'; ?>">ອະນຸມັດແລ້ວ</a>
                    <a href="index.php?status=Cancelled" class="btn rounded-pill px-4 <?php echo ($status_filter == 'Cancelled') ? 'btn-danger' : 'btn-light'; ?>">ຍົກເລີກແລ້ວ</a>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <form action="" method="GET" class="input-group shadow-sm rounded-pill overflow-hidden border">
                    <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
                    <input type="text" name="search" class="form-control border-0 px-3 shadow-none" placeholder="ຄົ້ນຫາຊື່ລູກຄ້າ..." value="<?php echo $search; ?>">
                    <button class="btn btn-white bg-white border-0" type="submit"><i class="fas fa-search text-muted"></i></button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ລູກຄ້າ / ເບີໂທ</th>
                            <th>ທົວ</th>
                            <th class="text-center">ຈຳນວນ</th>
                            <th class="text-end">ລາຄາລວມ</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 3. SQL Query: ດຶງຂໍ້ມູນການຈອງ ພ້ອມກັບດຶງຮູບສະລິບມາເຊັກ (Subquery)
                        $sql = "SELECT b.*, c.fullname, c.phone, t.tour_name, 
                                (SELECT payment_slip FROM payments WHERE booking_id = b.booking_id LIMIT 1) as slip
                                FROM bookings b
                                JOIN customers c ON b.customer_id = c.customer_id
                                JOIN tours t ON b.tour_id = t.tour_id";
                        
                        $where_clauses = [];
                        if ($status_filter != 'all') {
                            $where_clauses[] = "b.status = '$status_filter'";
                        }
                        if ($search != '') {
                            $where_clauses[] = "(c.fullname LIKE '%$search%' OR t.tour_name LIKE '%$search%')";
                        }

                        if (count($where_clauses) > 0) {
                            $sql .= " WHERE " . implode(' AND ', $where_clauses);
                        }

                        $sql .= " ORDER BY b.booking_id DESC";
                        $result = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                                $status = $row['status'];
                                $has_slip = !empty($row['slip']);
                                $booking_id = $row['booking_id'];
                                $wa_msg = "ສະບາຍດີ " . $row['fullname'] . ", ຂໍແຈ້ງສະຖານະການຈອງທົວຂອງທ່ານ...";
                                $wa_url = "https://wa.me/856" . str_replace([' ', '-', '020'], '', $row['phone']) . "?text=" . urlencode($wa_msg);
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">
                                        <!-- ສະແດງໄອຄອນສະລິບ ຖ້າມີຂໍ້ມູນ -->
                                        <?php if($has_slip): ?>
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#slipModal<?php echo $booking_id; ?>">
                                                <i class="fas fa-file-invoice-dollar text-success me-1" title="ເບິ່ງສະລິບການໂອນ"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php echo $row['fullname']; ?>
                                    </div>
                                    <small class="text-muted small"><?php echo $row['phone']; ?></small>
                                </td>
                                <td><span class="small"><?php echo $row['tour_name']; ?></span></td>
                                <td class="text-center small"><?php echo $row['num_people']; ?></td>
                                <td class="text-end fw-bold text-danger"><?php echo number_format($row['total_price']); ?></td>
                                <td class="text-center">
                                    <?php 
                                    if ($status == 'Confirmed') echo '<span class="badge rounded-pill bg-success px-3 py-2 small">ຢືນຢັນແລ້ວ</span>';
                                    elseif ($status == 'Cancelled') echo '<span class="badge rounded-pill bg-danger px-3 py-2 small">ຍົກເລີກແລ້ວ</span>';
                                    else echo '<span class="badge rounded-pill bg-warning text-dark px-3 py-2 small">ລໍຖ້າອະນຸມັດ</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                        <?php if($status == 'Pending'): ?>
                                            <a href="javascript:void(0)" onclick="confirmApprove(<?php echo $booking_id; ?>, 'approve.php?status=<?php echo $status_filter; ?>')" 
                                               class="btn btn-sm <?php echo $has_slip ? 'btn-success text-white' : 'btn-white text-muted'; ?> border-end" title="ອະນຸມັດ">
                                                <i class="fas fa-check-circle"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if($status != 'Cancelled'): ?>
                                            <a href="javascript:void(0)" onclick="confirmCancel(<?php echo $booking_id; ?>, 'cancel.php?status=<?php echo $status_filter; ?>')" 
                                               class="btn btn-sm btn-white text-secondary border-end" title="ຍົກເລີກ">
                                                <i class="fas fa-times-circle"></i>
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?php echo $wa_url; ?>" target="_blank" class="btn btn-sm btn-white text-info border-end" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                        <a href="view.php?id=<?php echo $booking_id; ?>" class="btn btn-sm btn-white text-primary border-end" title="ເບິ່ງ"><i class="fas fa-eye"></i></a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $booking_id; ?>, 'delete.php?status=<?php echo $status_filter; ?>')" class="btn btn-sm btn-white text-danger" title="ລຶບ"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal ສະແດງສະລິບຂອງແຕ່ລະລາຍການ -->
                            <?php if($has_slip): ?>
                            <div class="modal fade" id="slipModal<?php echo $booking_id; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow-lg">
                                        <div class="modal-header border-0 bg-light">
                                            <h5 class="modal-title fw-bold text-dark">
                                                <i class="fas fa-image text-success me-2"></i>ຫຼັກຖານການໂອນເງິນ
                                            </h5>
                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center p-4">
                                            <p class="mb-3 text-muted small">ລູກຄ້າ: <strong><?php echo $row['fullname']; ?></strong> | ຍອດເງິນ: <strong><?php echo number_format($row['total_price']); ?> ກີບ</strong></p>
                                            <a href="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['slip']; ?>" target="_blank">
                                                <img src="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['slip']; ?>" class="img-fluid rounded-3 shadow" style="max-height: 500px;">
                                            </a>
                                        </div>
                                        <div class="modal-footer border-0 bg-light justify-content-center">
                                            <?php if($status == 'Pending'): ?>
                                                <button onclick="confirmApprove(<?php echo $booking_id; ?>, 'approve.php?status=<?php echo $status_filter; ?>')" class="btn btn-success rounded-pill px-4 shadow-sm">ອະນຸມັດລາຍການນີ້</button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ປິດ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                        <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ບໍ່ມີຂໍ້ມູນໃນໝວດໝູ່ນີ້</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
    .btn-white { background: #fff; border: none; }
    .btn-white:hover { background: #f8f9fa; }
    .table thead th { font-size: 0.7rem; }
    .badge { font-weight: 500; }
</style>

<?php include '../../includes/footer.php'; ?>