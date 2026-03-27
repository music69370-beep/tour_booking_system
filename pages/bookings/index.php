<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
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
                        $sql = "SELECT b.*, c.fullname, c.phone, t.tour_name, 
                                (SELECT payment_slip FROM payments WHERE booking_id = b.booking_id LIMIT 1) as slip
                                FROM bookings b
                                JOIN customers c ON b.customer_id = c.customer_id
                                JOIN tours t ON b.tour_id = t.tour_id
                                ORDER BY b.booking_id DESC";
                        $result = mysqli_query($conn, $sql);

                        while($row = mysqli_fetch_assoc($result)):
                            $status = $row['status'];
                            $has_slip = !empty($row['slip']);
                            $booking_id = $row['booking_id'];
                            $wa_msg = "ສະບາຍດີ " . $row['fullname'] . ", ຂ້ອຍຕິດຕໍ່ຈາກ TourBooking...";
                            $wa_url = "https://wa.me/856" . str_replace([' ', '-', '020'], '', $row['phone']) . "?text=" . urlencode($wa_msg);
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">
                                        <?php if($has_slip): ?>
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#slipModal<?php echo $booking_id; ?>">
                                                <i class="fas fa-file-invoice-dollar text-success me-1" title="ເບິ່ງສະລິບ"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php echo $row['fullname']; ?>
                                    </div>
                                    <small class="text-muted"><?php echo $row['phone']; ?></small>
                                </td>
                                <td><?php echo $row['tour_name']; ?></td>
                                <td class="text-center"><?php echo $row['num_people']; ?> ຄົນ</td>
                                <td class="text-end fw-bold text-danger"><?php echo number_format($row['total_price']); ?></td>
                                <td class="text-center">
                                    <?php 
                                    if ($status == 'Confirmed') echo '<span class="badge rounded-pill bg-success px-3 py-2">ຢືນຢັນແລ້ວ</span>';
                                    elseif ($status == 'Cancelled') echo '<span class="badge rounded-pill bg-danger px-3 py-2">ຍົກເລີກແລ້ວ</span>';
                                    else echo '<span class="badge rounded-pill bg-warning text-dark px-3 py-2">ລໍຖ້າອະນຸມັດ</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                        <!-- ປຸ່ມອະນຸມັດ -->
                                        <?php if($status == 'Pending'): ?>
                                            <a href="javascript:void(0)" onclick="confirmApprove(<?php echo $booking_id; ?>, 'approve.php')" 
                                               class="btn btn-sm <?php echo $has_slip ? 'btn-success' : 'btn-white text-muted'; ?> border-end">
                                                <i class="fas fa-check-circle"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- ປຸ່ມຍົກເລີກ: ໂຊສະເພາະລາຍການທີ່ຍັງບໍ່ທັນຖືກຍົກເລີກ -->
                                        <?php if($status != 'Cancelled'): ?>
                                            <a href="javascript:void(0)" onclick="confirmCancel(<?php echo $booking_id; ?>, 'cancel.php')" 
                                               class="btn btn-sm btn-white text-secondary border-end" title="ຍົກເລີກການຈອງ">
                                                <i class="fas fa-times-circle"></i>
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?php echo $wa_url; ?>" target="_blank" class="btn btn-sm btn-white text-info border-end"><i class="fab fa-whatsapp"></i></a>
                                        <a href="view.php?id=<?php echo $booking_id; ?>" class="btn btn-sm btn-white text-primary border-end"><i class="fas fa-eye"></i></a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $booking_id; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal ສະແດງສະລິບ -->
                            <?php if($has_slip): ?>
                            <div class="modal fade" id="slipModal<?php echo $booking_id; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow-lg">
                                        <div class="modal-header border-0 bg-light">
                                            <h5 class="modal-title fw-bold text-dark">ຫຼັກຖານການໂອນ</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center p-4">
                                            <img src="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['slip']; ?>" class="img-fluid rounded-3 shadow" style="max-height: 500px;">
                                        </div>
                                        <div class="modal-footer border-0 bg-light justify-content-center">
                                            <?php if($status == 'Pending'): ?>
                                                <button onclick="confirmApprove(<?php echo $booking_id; ?>, 'approve.php')" class="btn btn-success rounded-pill px-4 shadow-sm">ອະນຸມັດເລີຍ</button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ປິດ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<style> .btn-white { background: #fff; border: none; } .btn-white:hover { background: #f8f9fa; } </style>
<?php include '../../includes/footer.php'; ?>