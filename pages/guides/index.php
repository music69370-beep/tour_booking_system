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
                            // SQL ໂຕນີ້ຈະໄປນັບທົວທັງໝົດທີ່ Guide ຄົນນີ້ຖືກມອບໝາຍໃຫ້ (ບໍ່ວ່າຈະ status ໃດກໍຕາມເພື່ອທົດສອບ)
                            $sql = "SELECT g.*, 
                                   (SELECT COUNT(*) FROM tours WHERE guide_id = g.guide_id AND status = 'Active') as active_tours_count
                                   FROM guides g 
                                   ORDER BY g.guide_id DESC";
                            
                            $res = mysqli_query($conn, $sql);
                            if(mysqli_num_rows($res) > 0):
                                while($row = mysqli_fetch_assoc($res)):
                                    
                                    // Logic ເຊັກສະຖານະ: ຖ້າມີທົວທີ່ Active ຕັ້ງແຕ່ 1 ອັນຂຶ້ນໄປ ໃຫ້ຖືວ່າ Busy
                                    $is_busy = ($row['active_tours_count'] > 0);
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <?php if($row['image']): ?>
                                        <img src="<?php echo BASE_URL; ?>assets/uploads/guides/<?php echo $row['image']; ?>" class="rounded-circle border shadow-sm" width="45" height="45" style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 45px; height: 45px;"><i class="fas fa-user text-muted"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $row['fullname']; ?></div>
                                    <small class="text-primary small">ID: <?php echo $row['license_id']; ?></small>
                                </td>
                                <td>
                                    <div class="small"><i class="fas fa-phone-alt me-1 text-muted"></i> <?php echo $row['phone']; ?></div>
                                    <div class="small"><i class="fas fa-envelope me-1 text-muted"></i> <?php echo $row['email']; ?></div>
                                </td>
                                <td>
                                    <div class="badge bg-light text-dark border fw-normal"><?php echo $row['specialization']; ?></div>
                                    <div class="small text-muted mt-1"><?php echo $row['exp_years']; ?> ປີ ປະສົບການ</div>
                                </td>
                                <td>
                                    <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($row['license_expiry'])); ?></small>
                                </td>
                                <td>
                                    <?php if($is_busy): ?>
                                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                                            <i class="fas fa-bus me-1"></i> ຕິດວຽກ (<?php echo $row['active_tours_count']; ?> ທົວ)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-success px-3 py-2">
                                            <i class="fas fa-check me-1"></i> ຫວ່າງ
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                        <button class="btn btn-sm btn-white text-info border-end" data-bs-toggle="modal" data-bs-target="#viewGuide<?php echo $row['guide_id']; ?>"><i class="fas fa-eye"></i></button>
                                        <a href="edit.php?id=<?php echo $row['guide_id']; ?>" class="btn btn-sm btn-white text-warning border-end"><i class="fas fa-edit"></i></a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['guide_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນໄກ້</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<style> .btn-white { background: #fff; border: none; } .btn-white:hover { background: #f8f9fa; } </style>
<?php include '../../includes/footer.php'; ?>