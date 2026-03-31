<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// ຮັບຄ່າຄົ້ນຫາ (ຖ້າມີ)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-map-marked-alt text-primary me-2"></i>ລາຍງານແພັກເກັດທົວທັງໝົດ</h2>
            <div class="d-flex gap-2">
                <form action="" method="GET" class="input-group shadow-sm rounded-pill overflow-hidden border">
                    <input type="text" name="search" class="form-control border-0 px-3 shadow-none" placeholder="ຄົ້ນຫາຊື່ ຫຼື ລະຫັດ..." value="<?php echo $search; ?>">
                    <button class="btn btn-white bg-white border-0" type="submit"><i class="fas fa-search text-muted"></i></button>
                </form>
                <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">+ ເພີ່ມທົວໃໝ່</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">ຮູບພາບ</th>
                                <th>ຂໍ້ມູນແພັກເກັດ</th>
                                <th>ພາຫະນະ / ໄກ້</th>
                                <th class="text-center">ບ່ອນນັ່ງ (ຫວ່າງ)</th>
                                <th class="text-end">ລາຄາຂາຍ/ທ່ານ</th>
                                <th class="text-center">ສະຖານະ</th>
                                <th class="text-center">ຈັດການ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // SQL ດຶງຂໍ້ມູນທົວ ພ້ອມ Join ເອົາຂໍ້ມູນລົດ ແລະ ໄກ້
                            $sql = "SELECT t.*, v.plate_number, v.model as car_model, g.fullname as guide_name 
                                    FROM tours t
                                    LEFT JOIN vehicles v ON t.vehicle_id = v.vehicle_id
                                    LEFT JOIN guides g ON t.guide_id = g.guide_id";
                            
                            if ($search != '') {
                                $sql .= " WHERE t.tour_name LIKE '%$search%' OR t.tour_code LIKE '%$search%'";
                            }
                            
                            $sql .= " ORDER BY t.tour_id DESC";
                            $result = mysqli_query($conn, $sql);

                            if(mysqli_num_rows($result) > 0):
                                while($row = mysqli_fetch_assoc($result)):
                                    // ຄຳນວນບ່ອນນັ່ງຫວ່າງ
                                    $tid = $row['tour_id'];
                                    $booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
                                    $booked_count = $booked_res['total'] ?? 0;
                                    $remaining = $row['max_seats'] - $booked_count;
                                    
                                    // ຄຳນວນກຳໄລເບື້ອງຕົ້ນຕໍ່ຄົນ
                                    $profit_per_pax = $row['price'] - $row['cost_per_person'];
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <img src="<?php echo BASE_URL; ?>assets/uploads/tours/<?php echo $row['image']; ?>" 
                                             class="rounded-3 border shadow-sm" width="80" height="50" style="object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo $row['tour_name']; ?></div>
                                        <div class="d-flex gap-2 mt-1">
                                            <span class="badge bg-light text-primary border small fw-normal"><?php echo $row['tour_code']; ?></span>
                                            <span class="badge bg-primary-subtle text-primary small fw-normal"><?php echo $row['category']; ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-dark mb-1">
                                            <i class="fas fa-bus me-1 text-muted"></i> <?php echo $row['car_model'] ?: 'ຍັງບໍ່ໄດ້ກຳນົດ'; ?>
                                            <span class="text-muted small">(<?php echo $row['plate_number']; ?>)</span>
                                        </div>
                                        <div class="small text-dark">
                                            <i class="fas fa-user-tie me-1 text-muted"></i> <?php echo $row['guide_name'] ?: 'ຍັງບໍ່ມີໄກ້'; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold <?php echo ($remaining <= 2) ? 'text-danger' : 'text-success'; ?>">
                                            <?php echo $remaining; ?> / <?php echo $row['max_seats']; ?>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.7rem;">ຂັ້ນຕ່ຳ: <?php echo $row['min_pax']; ?> ຄົນ</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-danger"><?php echo number_format($row['price']); ?></div>
                                        <small class="text-success fw-bold" style="font-size: 0.7rem;">ກຳໄລ: +<?php echo number_format($profit_per_pax); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill <?php echo ($row['status'] == 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ($row['status'] == 'Active') ? 'ເປີດ' : 'ປິດ'; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                            <!-- ປຸ່ມເບິ່ງລາຍລະອຽດທັງໝົດ (Modal) -->
                                            <button class="btn btn-sm btn-white text-info border-end" data-bs-toggle="modal" data-bs-target="#viewTour<?php echo $tid; ?>" title="ເບິ່ງລາຍລະອຽດ">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="edit.php?id=<?php echo $tid; ?>" class="btn btn-sm btn-white text-warning border-end" title="ແກ້ໄຂ">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" 
                                               onclick="confirmDelete(<?php echo $tid; ?>, 'delete.php')" 
                                               class="btn btn-sm btn-white text-danger" title="ລຶບ">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal ສະແດງຂໍ້ມູນທົວແບບລະອຽດ (ປັບປຸງໃຫ້ໂຊວັນທີ) -->
                                <div class="modal fade" id="viewTour<?php echo $tid; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content rounded-5 border-0 shadow-lg">
                                            <div class="modal-header border-0 bg-primary text-white p-4">
                                                <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i>ລາຍລະອຽດແພັກເກັດທົວ</h5>
                                                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 p-lg-5">
                                                <div class="row g-4">
                                                    <div class="col-md-5 text-center">
                                                        <img src="<?php echo BASE_URL; ?>assets/uploads/tours/<?php echo $row['image']; ?>" class="img-fluid rounded-4 shadow-sm mb-3">
                                                        <div class="bg-light p-3 rounded-4 text-start">
                                                            <h6 class="fw-bold text-primary mb-2 small text-uppercase">ຈຸດເດັ່ນ (Highlights)</h6>
                                                            <p class="small text-muted mb-0" style="white-space: pre-line;"><?php echo $row['highlights']; ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <h4 class="fw-bold mb-1 text-dark"><?php echo $row['tour_name']; ?></h4>
                                                        <p class="text-muted small mb-4">ID: <?php echo $row['tour_code']; ?> | <?php echo $row['category']; ?></p>
                                                        
                                                        <div class="row g-3">
                                                            <!-- ສ່ວນທີ່ເພີ່ມໃໝ່: ວັນທີເລີ່ມ ແລະ ວັນທີສິ້ນສຸດ -->
                                                            <div class="col-6">
                                                                <small class="text-muted">ວັນທີເລີ່ມເດີນທາງ:</small>
                                                                <p class="fw-bold mb-0 text-success">
                                                                    <i class="fas fa-calendar-check me-1"></i> <?php echo date('d/m/Y', strtotime($row['start_date'])); ?>
                                                                </p>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted">ວັນທີສິ້ນສຸດທົວ:</small>
                                                                <p class="fw-bold mb-0 text-danger">
                                                                    <i class="fas fa-calendar-times me-1"></i> <?php echo date('d/m/Y', strtotime($row['end_date'])); ?>
                                                                </p>
                                                            </div>

                                                            <div class="col-6"><small class="text-muted">ໄລຍະເວລາ:</small><p class="fw-bold mb-0"><?php echo $row['duration']; ?></p></div>
                                                            <div class="col-6"><small class="text-muted">ອາຫານ:</small><p class="fw-bold mb-0"><?php echo $row['meals']; ?> ຄາບ</p></div>
                                                            <div class="col-12"><small class="text-muted">ສະຖານທີ່ນັດພົບ:</small><p class="fw-bold mb-0 text-dark"><?php echo $row['meeting_point']; ?></p></div>
                                                            
                                                            <div class="col-12"><hr class="my-2"></div>
                                                            
                                                            <div class="col-6">
                                                                <h6 class="text-success fw-bold small text-uppercase">ສິ່ງທີ່ລວມ:</h6>
                                                                <div class="small text-muted" style="white-space: pre-line; font-size: 0.75rem;"><?php echo $row['whats_included']; ?></div>
                                                            </div>
                                                            <div class="col-6">
                                                                <h6 class="text-danger fw-bold small text-uppercase">ບໍ່ລວມ:</h6>
                                                                <div class="small text-muted" style="white-space: pre-line; font-size: 0.75rem;"><?php echo $row['whats_excluded']; ?></div>
                                                            </div>
                                                            
                                                            <div class="col-12 border-top pt-2">
                                                                <h6 class="text-warning fw-bold small text-uppercase">ນະໂຍບາຍການຍົກເລີກ:</h6>
                                                                <p class="small text-muted italic mb-0" style="font-size: 0.7rem;"><?php echo $row['cancellation_policy']; ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endwhile; else: ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນແພັກເກັດທົວ</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .btn-white { background-color: #fff; border: none; }
    .btn-white:hover { background-color: #f8f9fa; }
    .bg-primary-subtle { background-color: #e7f1ff; }
</style>

<?php include '../../includes/footer.php'; ?>