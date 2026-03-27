<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-user-tie text-primary me-2"></i>ຈັດການໄກ້ຜູ້ນຳທ່ຽວ</h2>
            <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມໄກ້ໃໝ່
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">ຮູບ</th>
                                <th>ຊື່ ແລະ ເລກບັດ</th>
                                <th>ການຕິດຕໍ່</th>
                                <th>ຄວາມຊຳນານ / ປະສົບການ</th>
                                <th>ວັນໝົດອາຍຸບັດ</th>
                                <th>ສະຖານະປັດຈຸບັນ</th>
                                <th class="text-center">ຈັດການ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query ຂໍ້ມູນ ພ້ອມນັບຈຳນວນທົວທີ່ Active ຢູ່
                            $sql = "SELECT g.*, 
                                   (SELECT COUNT(*) FROM tours WHERE guide_id = g.guide_id AND status = 'Active') as active_tours
                                   FROM guides g 
                                   ORDER BY g.guide_id DESC";
                            
                            $res = mysqli_query($conn, $sql);
                            if(mysqli_num_rows($res) > 0):
                                while($row = mysqli_fetch_assoc($res)):
                                    $is_busy = ($row['active_tours'] > 0);
                                    
                                    // ກວດສອບວັນໝົດອາຍຸບັດໄກ້
                                    $warning_date = date('Y-m-d', strtotime('+30 days'));
                                    $is_expired = ($row['license_expiry'] <= date('Y-m-d'));
                                    $is_warning = ($row['license_expiry'] <= $warning_date && !$is_expired);
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <?php if(!empty($row['image'])): ?>
                                        <img src="<?php echo BASE_URL; ?>assets/uploads/guides/<?php echo $row['image']; ?>" class="rounded-circle border shadow-sm" width="45" height="45" style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 45px; height: 45px;"><i class="fas fa-user text-muted small"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $row['fullname']; ?></div>
                                    <small class="text-primary small">ບັດເລກທີ: <?php echo $row['license_id']; ?></small>
                                </td>
                                <td>
                                    <div class="small"><i class="fas fa-phone-alt me-1 text-muted small"></i> <?php echo $row['phone']; ?></div>
                                    <div class="small text-muted"><?php echo $row['email']; ?></div>
                                </td>
                                <td>
                                    <div class="badge bg-light text-dark border fw-normal"><?php echo $row['specialization']; ?></div>
                                    <div class="small text-muted mt-1"><?php echo $row['exp_years']; ?> ປີ ປະສົບການ</div>
                                </td>
                                <td>
                                    <span class="small <?php echo ($is_expired || $is_warning) ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                        <i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($row['license_expiry'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($is_busy): ?>
                                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                                            <i class="fas fa-bus me-1"></i> ຕິດວຽກ (<?php echo $row['active_tours']; ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-success px-3 py-2">
                                            <i class="fas fa-check me-1"></i> ຫວ່າງ
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                        <!-- 1. ປຸ່ມເບິ່ງ (ຮູບຕາ) -->
                                        <button class="btn btn-sm btn-white text-info border-end" data-bs-toggle="modal" data-bs-target="#viewGuide<?php echo $row['guide_id']; ?>" title="ເບິ່ງລາຍລະອຽດ">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <!-- 2. ປຸ່ມແກ້ໄຂ -->
                                        <a href="edit.php?id=<?php echo $row['guide_id']; ?>" class="btn btn-sm btn-white text-warning border-end" title="ແກ້ໄຂ">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- 3. ປຸ່ມລຶບ -->
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['guide_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger" title="ລຶບ">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal ສະແດງຂໍ້ມູນໄກ້ທັງໝົດ -->
                            <div class="modal fade" id="viewGuide<?php echo $row['guide_id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content rounded-5 border-0 shadow-lg">
                                        <div class="modal-header border-0 bg-primary text-white p-4">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-id-card-alt me-2"></i>ລາຍລະອຽດຂໍ້ມູນໄກ້ຜູ້ນຳທ່ຽວ</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4 p-lg-5 bg-white">
                                            <div class="row g-4">
                                                <!-- ເບື້ອງຊ້າຍ: Profile -->
                                                <div class="col-md-4 text-center border-end">
                                                    <?php if($row['image']): ?>
                                                        <img src="<?php echo BASE_URL; ?>assets/uploads/guides/<?php echo $row['image']; ?>" class="img-fluid rounded-4 shadow-sm border mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                                    <?php endif; ?>
                                                    <h4 class="fw-bold mb-1"><?php echo $row['fullname']; ?></h4>
                                                    <p class="text-muted small mb-3">ID: <?php echo $row['license_id']; ?></p>
                                                    <div class="bg-light p-3 rounded-4 text-start">
                                                        <small class="text-muted d-block">ສະຖານະປັດຈຸບັນ:</small>
                                                        <strong class="<?php echo ($is_busy)?'text-warning':'text-success'; ?>">
                                                            <?php echo ($is_busy)?'ຕິດວຽກທົວ (Busy)':'ຫວ່າງ (Available)'; ?>
                                                        </strong>
                                                    </div>
                                                </div>

                                                <!-- ເບື້ອງຂວາ: Details -->
                                                <div class="col-md-8 ps-md-4">
                                                    <div class="row g-4">
                                                        <!-- Professional -->
                                                        <div class="col-6">
                                                            <h6 class="fw-bold text-primary border-bottom pb-2">ຂໍ້ມູນວິຊາຊີບ</h6>
                                                            <p class="mb-1 small">ພາສາ: <strong><?php echo $row['languages']; ?></strong></p>
                                                            <p class="mb-1 small">ຄວາມຊຳນານ: <strong><?php echo $row['specialization']; ?></strong></p>
                                                            <p class="mb-1 small">ປະສົບການ: <strong><?php echo $row['exp_years']; ?> ປີ</strong></p>
                                                            <p class="mb-0 small">ບັດໝົດອາຍຸ: <strong class="text-danger"><?php echo date('d/m/Y', strtotime($row['license_expiry'])); ?></strong></p>
                                                        </div>

                                                        <!-- Payment -->
                                                        <div class="col-6">
                                                            <h6 class="fw-bold text-success border-bottom pb-2">ການເງິນ & ສຸຂະພາບ</h6>
                                                            <p class="mb-1 small">ທະນາຄານ: <strong><?php echo $row['bank_name']; ?></strong></p>
                                                            <p class="mb-1 small">ເລກບັນຊີ: <strong><?php echo $row['bank_account']; ?></strong></p>
                                                            <p class="mb-0 small">ປະຖົມພະຍາບານ: <strong><?php echo ($row['first_aid_certified']) ? 'ຜ່ານການຢັ້ງຢືນ ✅' : 'ບໍ່ມີ ❌'; ?></strong></p>
                                                        </div>

                                                        <!-- Emergency -->
                                                        <div class="col-12">
                                                            <h6 class="fw-bold text-danger border-bottom pb-2">ຂໍ້ມູນສຸກເສີນ</h6>
                                                            <p class="mb-1 small">ຊື່ຜູ້ຕິດຕໍ່: <strong><?php echo $row['emergency_contact_name']; ?></strong></p>
                                                            <p class="mb-0 small">ເບີໂທສຸກເສີນ: <strong><?php echo $row['emergency_contact_phone']; ?></strong></p>
                                                        </div>

                                                        <!-- Document Link -->
                                                        <?php if($row['doc_attachment']): ?>
                                                        <div class="col-12 mt-2">
                                                            <a href="<?php echo BASE_URL; ?>assets/uploads/guides/<?php echo $row['doc_attachment']; ?>" target="_blank" class="btn btn-outline-dark btn-sm w-100 rounded-pill">
                                                                <i class="fas fa-file-pdf me-2"></i> ເປີດເບິ່ງເອກະສານຕິດຄັດ (License/Cert)
                                                            </a>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 bg-light p-3">
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ປິດໜ້າຈໍ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php endwhile; else: ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນໄກ້ຜູ້ນຳທ່ຽວ</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .btn-white { background: #fff; border: none; }
    .btn-white:hover { background: #f8f9fa; }
    .table thead th { font-weight: 600; font-size: 0.75rem; letter-spacing: 0.5px; }
    .modal-body p { margin-bottom: 0.5rem; }
</style>

<?php include '../../includes/footer.php'; ?>