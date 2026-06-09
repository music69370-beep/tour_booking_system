<?php 
include '../../config/db.php';
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="px-4 pb-5">
        <!-- ຫົວຂໍ້ ແລະ ປຸ່ມເພີ່ມ -->
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-id-card-alt text-primary me-2"></i>ລາຍຊື່ຄົນຂັບທັງໝົດ</h2>
            <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມຄົນຂັບໃໝ່
            </a>
        </div>

        <!-- ຕາຕະລາງລາຍງານ -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ຮູບ</th>
                            <th>ຊື່ ແລະ ເບີໂທ</th>
                            <th>ຂໍ້ມູນໃບຂັບຂີ່</th>
                            <th>ວັນໝົດອາຍຸບັດ</th>
                            <th>ປະສົບການ</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM drivers ORDER BY driver_id DESC";
                        $res = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)):
                                // ກວດເຊັກຮູບໂປຣຟາຍ
                                $img_path = "../../assets/uploads/drivers/" . $row['image'];
                                $display_img = (!empty($row['image']) && file_exists($img_path)) ? $img_path : "../../assets/img/default-user.png";
                                
                                // ກວດເຊັກວັນໝົດອາຍຸ (ຖ້າໝົດອາຍຸໃຫ້ເປັນສີແດງ)
                                $expiry_date = $row['license_expiry'];
                                $is_expired = (strtotime($expiry_date) < time());
                        ?>
                        <tr>
                            <td class="ps-4">
                                <img src="<?php echo $display_img; ?>" class="rounded-circle border shadow-sm" width="50" height="50" style="object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo $row['fullname']; ?></div>
                                <small class="text-muted"><i class="fas fa-phone-alt me-1"></i> <?php echo $row['phone']; ?></small>
                            </td>
                            <td>
                                <div class="small fw-bold">ເລກທີ: <?php echo $row['license_number']; ?></div>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 small">ປະເພດ <?php echo $row['license_type']; ?></span>
                            </td>
                            <td>
                                <span class="<?php echo $is_expired ? 'text-danger fw-bold' : 'text-dark'; ?>">
                                    <i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($expiry_date)); ?>
                                    <?php if($is_expired) echo '<br><small class="badge bg-danger">ໝົດອາຍຸແລ້ວ</small>'; ?>
                                </span>
                            </td>
                            <td><?php echo $row['experience_years']; ?> ປີ</td>
                            <td class="text-center">
                                <?php 
                                    $status = $row['status'];
                                    $bg = ($status == 'Available') ? 'bg-success' : (($status == 'Busy') ? 'bg-warning text-dark' : 'bg-secondary');
                                    $label = ($status == 'Available') ? 'ວ່າງ' : (($status == 'Busy') ? 'ຕິດວຽກ' : 'ພັກຜ່ອນ');
                                ?>
                                <span class="badge rounded-pill <?php echo $bg; ?> px-3 py-2"><?php echo $label; ?></span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                    <a href="edit.php?id=<?php echo $row['driver_id']; ?>" class="btn btn-sm btn-white text-warning border-end" title="ແກ້ໄຂ">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['driver_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger" title="ລຶບ">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                                    <p>ຍັງບໍ່ມີຂໍ້ມູນຄົນຂັບໃນລະບົບ</p>
                                </td>
                            </tr>
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
    .badge { font-weight: 500; }
</style>

<?php include '../../includes/footer.php'; ?>