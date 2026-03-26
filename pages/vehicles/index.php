<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h2 class="fw-bold"><i class="fas fa-bus text-info me-2"></i>ຈັດການພາຫະນະ ແລະ ຄົນຂັບ</h2>
        <a href="add.php" class="btn btn-info text-white rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> ເພີ່ມລົດໃໝ່
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ຂໍ້ມູນລົດ</th>
                            <th>ປະເພດ / ບ່ອນນັ່ງ</th>
                            <th>ຄົນຂັບ / ເບີໂທ</th>
                            <th>ວັນໝົດອາຍຸປະກັນ</th>
                            <th>ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM vehicles ORDER BY vehicle_id DESC");
                        if(mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)):
                                // ກວດສອບວັນໝົດອາຍຸປະກັນໄພ (ຖ້າເຫຼືອໜ້ອຍກວ່າ 30 ວັນ ໃຫ້ເຕືອນສີແດງ)
                                $today = date('Y-m-d');
                                $warning_date = date('Y-m-d', strtotime('+30 days'));
                                $is_expiring = ($row['insurance_expiry'] <= $warning_date);
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?php echo $row['model']; ?></div>
                                    <span class="badge bg-light text-dark border small"><?php echo $row['plate_number']; ?></span>
                                </td>
                                <td>
                                    <div class="small text-muted"><?php echo $row['vehicle_type']; ?></div>
                                    <div class="fw-bold small text-primary"><?php echo $row['capacity']; ?> ບ່ອນນັ່ງ</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <!-- ສະແດງຮູບຄົນຂັບ -->
                                        <?php if(!empty($row['driver_image'])): ?>
                                            <img src="<?php echo BASE_URL; ?>assets/uploads/vehicles/<?php echo $row['driver_image']; ?>" 
                                                 class="rounded-circle me-2 border shadow-sm" width="40" height="40" style="object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold small"><?php echo $row['driver_name']; ?></div>
                                            <small class="text-muted small"><?php echo $row['driver_phone']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="small <?php echo ($is_expiring) ? 'text-danger fw-bold' : ''; ?>">
                                        <i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($row['insurance_expiry'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill <?php 
                                        echo ($row['status'] == 'Available') ? 'bg-success' : (($row['status'] == 'Busy') ? 'bg-warning text-dark' : 'bg-danger'); 
                                    ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                        <button class="btn btn-sm btn-white text-info border-end" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $row['vehicle_id']; ?>" title="ເບິ່ງລາຍລະອຽດ">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="edit.php?id=<?php echo $row['vehicle_id']; ?>" class="btn btn-sm btn-white text-warning border-end" title="ແກ້ໄຂ">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['vehicle_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger" title="ລຶບ">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal ສະແດງລາຍລະອຽດຂໍ້ມູນທັງໝົດ -->
                            <div class="modal fade" id="viewModal<?php echo $row['vehicle_id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content rounded-4 border-0 shadow-lg">
                                        <div class="modal-header border-0 bg-info text-white">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i>ລາຍລະອຽດລົດ ແລະ ຄົນຂັບ</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row">
                                                <!-- ຂໍ້ມູນລົດ -->
                                                <div class="col-md-6 border-end">
                                                    <h6 class="fw-bold text-info mb-3 border-bottom pb-2">ຂໍ້ມູນພາຫະນະ</h6>
                                                    <p class="mb-1 small text-muted">ລຸ້ນລົດ:</p>
                                                    <p class="fw-bold mb-3"><?php echo $row['model']; ?></p>
                                                    
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <p class="mb-1 small text-muted">ເລກທະບຽນ:</p>
                                                            <p class="fw-bold"><?php echo $row['plate_number']; ?></p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-1 small text-muted">ປະເພດລົດ:</p>
                                                            <p class="fw-bold"><?php echo $row['vehicle_type']; ?></p>
                                                        </div>
                                                    </div>
                                                    
                                                    <p class="mb-1 small text-muted">ປະກັນໄພໝົດອາຍຸ:</p>
                                                    <p class="fw-bold <?php echo ($is_expiring) ? 'text-danger' : ''; ?>"><?php echo date('d/m/Y', strtotime($row['insurance_expiry'])); ?></p>
                                                    
                                                    <p class="mb-1 small text-muted">ອຸປະກອນເສີມ:</p>
                                                    <p class="small text-dark"><?php echo $row['amenities'] ?: '-'; ?></p>
                                                </div>

                                                <!-- ຂໍ້ມູນຄົນຂັບ -->
                                                <div class="col-md-6 ps-md-4">
                                                    <h6 class="fw-bold text-success mb-3 border-bottom pb-2">ຂໍ້ມູນຄົນຂັບ</h6>
                                                    <div class="text-center mb-3">
                                                        <!-- ຮູບຄົນຂັບໃນ Modal -->
                                                        <?php if(!empty($row['driver_image'])): ?>
                                                            <img src="<?php echo BASE_URL; ?>assets/uploads/vehicles/<?php echo $row['driver_image']; ?>" 
                                                                 class="rounded-3 border shadow-sm" width="120" height="120" style="object-fit: cover;">
                                                        <?php endif; ?>
                                                    </div>
                                                    <p class="mb-1 small text-muted">ຊື່ຄົນຂັບ:</p>
                                                    <p class="fw-bold mb-2"><?php echo $row['driver_name']; ?> (ປະສົບການ <?php echo $row['experience_years']; ?> ປີ)</p>
                                                    
                                                    <p class="mb-1 small text-muted">ເລກໃບຂັບຂີ່ / ໝົດອາຍຸ:</p>
                                                    <p class="fw-bold small"><?php echo $row['license_number']; ?> (<?php echo date('d/m/Y', strtotime($row['license_expiry'])); ?>)</p>
                                                    
                                                    <p class="mb-1 small text-muted">ຕິດຕໍ່ສຸກເສີນ:</p>
                                                    <p class="fw-bold text-danger small"><?php echo $row['emergency_contact']; ?></p>

                                                    <!-- ຮູບໃບຂັບຂີ່ໃນ Modal -->
                                                    <?php if(!empty($row['license_image'])): ?>
                                                        <div class="mt-3">
                                                            <p class="mb-1 small text-muted text-center">ຮູບໃບຂັບຂີ່:</p>
                                                            <a href="<?php echo BASE_URL; ?>assets/uploads/vehicles/<?php echo $row['license_image']; ?>" target="_blank">
                                                                <img src="<?php echo BASE_URL; ?>assets/uploads/vehicles/<?php echo $row['license_image']; ?>" 
                                                                     class="img-fluid rounded border shadow-sm" style="max-height: 100px;">
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ປິດໜ້າຈໍ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນພາຫະນະ</td></tr>
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
    .table thead th { font-weight: 600; font-size: 0.75rem; letter-spacing: 0.5px; }
    .badge { padding: 0.5em 1em; }
    .modal-body p { margin-bottom: 0.5rem; }
    .main-content { background-color: #f4f6f9; min-height: 100vh; }
</style>

<?php include '../../includes/footer.php'; ?>