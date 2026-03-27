<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-calendar-check text-primary me-2"></i>ລາຍການຈອງທົວ</h2>
            <div class="d-flex gap-2">
                <a href="export.php" class="btn btn-success rounded-pill px-3 shadow-sm"><i class="fas fa-file-excel me-1"></i> ສົ່ງອອກ Excel</a>
                <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">+ ສ້າງການຈອງໃໝ່</a>
            </div>
        </div>

        <!-- Filter Buttons & Search -->
        <div class="row mb-4 g-3">
            <div class="col-md-8">
                <div class="btn-group p-1 bg-white shadow-sm rounded-pill">
                    <a href="index.php?status=all" class="btn rounded-pill px-3 <?php echo ($status_filter == 'all') ? 'btn-primary shadow' : 'btn-light'; ?>">ທັງໝົດ</a>
                    <a href="index.php?status=Pending" class="btn rounded-pill px-3 <?php echo ($status_filter == 'Pending') ? 'btn-warning text-dark shadow' : 'btn-light'; ?>">ລໍຖ້າອະນຸມັດ</a>
                    <a href="index.php?status=Confirmed" class="btn rounded-pill px-3 <?php echo ($status_filter == 'Confirmed') ? 'btn-success shadow' : 'btn-light'; ?>">ອະນຸມັດແລ້ວ</a>
                    <a href="index.php?status=Cancelled" class="btn rounded-pill px-3 <?php echo ($status_filter == 'Cancelled') ? 'btn-danger shadow' : 'btn-light'; ?>">ຍົກເລີກແລ້ວ</a>
                </div>
            </div>
            <div class="col-md-4">
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
                            <th class="ps-4 py-3">ວັນທີເດີນທາງ</th>
                            <th>ລູກຄ້າ</th>
                            <th>ແພັກເກັດ / ໄກ້ / ລົດ</th>
                            <th class="text-end">ລາຄາລວມ</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT b.*, c.fullname, c.phone, t.tour_name, t.cost_per_person, v.plate_number, g.fullname as guide_name,
                                (SELECT payment_slip FROM payments WHERE booking_id = b.booking_id LIMIT 1) as slip
                                FROM bookings b
                                JOIN customers c ON b.customer_id = c.customer_id
                                JOIN tours t ON b.tour_id = t.tour_id
                                LEFT JOIN vehicles v ON t.vehicle_id = v.vehicle_id
                                LEFT JOIN guides g ON t.guide_id = g.guide_id";
                        
                        $where = [];
                        if ($status_filter != 'all') $where[] = "b.status = '$status_filter'";
                        if ($search != '') $where[] = "(c.fullname LIKE '%$search%' OR t.tour_name LIKE '%$search%')";
                        if (count($where) > 0) $sql .= " WHERE " . implode(' AND ', $where);
                        
                        $sql .= " ORDER BY b.travel_date ASC";
                        $result = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                                $status = $row['status'];
                                $bid = $row['booking_id'];
                                $has_slip = !empty($row['slip']);
                                $profit = ($status != 'Cancelled') ? ($row['total_price'] - ($row['cost_per_person'] * $row['num_people'])) : 0;
                                
                                // Checklist Progress
                                $t_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as tot, SUM(is_completed) as done FROM booking_tasks WHERE booking_id = $bid"));
                                $perc = ($t_res['tot'] > 0) ? round(($t_res['done'] / $t_res['tot']) * 100) : 0;
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-primary"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></div>
                                    <small class="text-muted" style="font-size: 0.7rem;">ID: #BK-<?php echo $bid; ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        <?php if($has_slip): ?>
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#slipModal<?php echo $bid; ?>"><i class="fas fa-file-invoice-dollar text-success me-1" title="ເບິ່ງສະລິບ"></i></a>
                                        <?php endif; ?>
                                        <?php echo $row['fullname']; ?>
                                    </div>
                                    <small class="text-muted"><?php echo $row['phone']; ?></small>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark"><?php echo $row['tour_name']; ?> (<?php echo $row['num_people']; ?> ຄົນ)</div>
                                    <div class="text-muted mb-1" style="font-size: 0.7rem;"><i class="fas fa-user-tie me-1"></i><?php echo $row['guide_name'] ?: 'ຍັງບໍ່ມີໄກ້'; ?> | <i class="fas fa-bus me-1"></i><?php echo $row['plate_number'] ?: 'ຍັງບໍ່ມີລົດ'; ?></div>
                                    <div class="progress" style="height: 4px; width: 100px;" title="ການກຽມຕົວ: <?php echo $perc; ?>%">
                                        <div class="progress-bar bg-warning" style="width: <?php echo $perc; ?>%"></div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-danger"><?php echo number_format($row['total_price']); ?></div>
                                    <small class="text-success" style="font-size: 0.7rem;"><?php echo ($status != 'Cancelled') ? 'ກຳໄລ: +'.number_format($profit) : '<span class="text-danger">ຍົກເລີກແລ້ວ</span>'; ?></small>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    if ($status == 'Confirmed') echo '<span class="badge rounded-pill bg-success px-3 py-2">ຢືນຢັນແລ້ວ</span>';
                                    elseif ($status == 'Cancelled') echo '<span class="badge rounded-pill bg-danger px-3 py-2">ຍົກເລີກແລ້ວ</span>';
                                    else echo '<span class="badge rounded-pill bg-warning text-dark px-3 py-2">ລໍຖ້າອະນຸມັດ</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                        <?php if($status == 'Pending'): ?>
                                            <a href="javascript:void(0)" onclick="confirmApprove(<?php echo $bid; ?>, 'approve.php?status=<?php echo $status_filter; ?>')" class="btn btn-sm <?php echo $has_slip ? 'btn-success text-white' : 'btn-white text-muted'; ?> border-end" title="ອະນຸມັດ"><i class="fas fa-check-circle"></i></a>
                                        <?php endif; ?>
                                        <?php if($status != 'Cancelled'): ?>
                                            <a href="cancel_form.php?id=<?php echo $bid; ?>" class="btn btn-sm btn-white text-secondary border-end" title="ຍົກເລີກ"><i class="fas fa-times-circle"></i></a>
                                        <?php endif; ?>
                                        <a href="view.php?id=<?php echo $bid; ?>" class="btn btn-sm btn-white text-primary border-end" title="ເບິ່ງລາຍລະອຽດ"><i class="fas fa-eye"></i></a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $bid; ?>, 'delete.php?status=<?php echo $status_filter; ?>')" class="btn btn-sm btn-white text-danger" title="ລຶບ"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Slip Modal -->
                            <?php if($has_slip): ?>
                            <div class="modal fade" id="slipModal<?php echo $bid; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow-lg"><div class="modal-body text-center p-4">
                                    <h5 class="fw-bold mb-3">ຫຼັກຖານການໂອນເງິນ</h5>
                                    <img src="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['slip']; ?>" class="img-fluid rounded-3 shadow" style="max-height: 500px;">
                                    <div class="mt-4"><button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ປິດ</button></div>
                                </div></div></div>
                            </div>
                            <?php endif; ?>

                        <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ບໍ່ມີຂໍ້ມູນ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<style> .btn-white { background: #fff; border: none; } .btn-white:hover { background: #f8f9fa; } </style>
<?php include '../../includes/footer.php'; ?>