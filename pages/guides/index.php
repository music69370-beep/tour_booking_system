<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// 1. ດຶງຂໍ້ມູນໄກ້ທັງໝົດ
$sql = "SELECT * FROM guides ORDER BY guide_id DESC";
$res = mysqli_query($conn, $sql);

// ສ້າງ Array ໄວ້ເກັບຂໍ້ມູນເພື່ອໄປສ້າງ Modal ທີຫຼັງ
$guide_list = [];
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-user-tie text-primary me-2"></i>ຈັດການໄກ້ຜູ້ນຳທ່ຽວ</h2>
            <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມໄກ້ໃໝ່
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ຮູບ</th>
                            <th>ຊື່ ແລະ ເລກບັດ</th>
                            <th>ການຕິດຕໍ່</th>
                            <th>ວັນໝົດອາຍຸບັດ</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)): 
                                $guide_list[] = $row; // ເກັບໃສ່ Array
                                $gid = $row['guide_id'];
                                
                                // ກວດສອບວຽກ
                                $q_work = mysqli_query($conn, "SELECT COUNT(*) as c FROM tour_assigned_guides tag JOIN tours t ON tag.tour_id = t.tour_id WHERE tag.guide_id = '$gid' AND t.status = 'Active'");
                                $work_data = mysqli_fetch_assoc($q_work);
                                $active_work = $work_data['c'];
                        ?>
                        <tr>
                            <td class="ps-4">
                                <?php 
                                    $img = $row['image'];
                                    $path = "../../assets/uploads/guides/" . $img;
                                    if(!empty($img) && file_exists($path)): ?>
                                    <img src="<?php echo $path; ?>" class="rounded-circle border shadow-sm" width="45" height="45" style="object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 45px; height: 45px;"><i class="fas fa-user text-muted"></i></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo $row['fullname']; ?></div>
                                <small class="text-primary">ID: <?php echo $row['license_id']; ?></small>
                            </td>
                            <td>
                                <div class="small"><?php echo $row['phone']; ?></div>
                                <div class="small text-muted"><?php echo $row['email']; ?></div>
                            </td>
                            <td>
                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($row['license_expiry'])); ?></small>
                            </td>
                            <td class="text-center">
                                <?php if($active_work > 0): ?>
                                    <span class="badge rounded-pill bg-warning text-dark px-3 py-2">ຕິດວຽກ (<?php echo $active_work; ?>)</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-success px-3 py-2 text-white">ວ່າງ</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm border rounded-pill overflow-hidden bg-white">
                                    <button type="button" class="btn btn-sm btn-white text-info border-end px-3" data-bs-toggle="modal" data-bs-target="#modalG<?php echo $gid; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="edit.php?id=<?php echo $gid; ?>" class="btn btn-sm btn-white text-warning border-end px-3"><i class="fas fa-edit"></i></a>
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $gid; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger px-3"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນໄກ້ໃນລະບົບ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- --- ສ່ວນຂອງ Modal (ຍ້າຍອອກມາໄວ້ນອກ Table ເພື່ອຄວາມປອດໄພ) --- -->
    <?php foreach($guide_list as $g_data): 
        $gid = $g_data['guide_id'];
    ?>
    <div class="modal fade" id="modalG<?php echo $gid; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 bg-primary text-white p-4">
                    <h5 class="modal-title fw-bold">ຕາຕະລາງຂອງ: <?php echo $g_data['fullname']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light small">
                                <tr>
                                    <th>ຊື່ແພັກເກັດທົວ</th>
                                    <th class="text-center">ວັນທີເດີນທາງ</th>
                                    <th class="text-center">ສະຖານະ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sql_t = "SELECT t.tour_name, t.start_date, t.end_date, t.status 
                                          FROM tour_assigned_guides tag
                                          JOIN tours t ON tag.tour_id = t.tour_id
                                          WHERE tag.guide_id = '$gid'
                                          ORDER BY t.start_date DESC";
                                $res_t = mysqli_query($conn, $sql_t);
                                if(mysqli_num_rows($res_t) > 0):
                                    while($t = mysqli_fetch_assoc($res_t)):
                                ?>
                                <tr>
                                    <td class="fw-bold"><?php echo $t['tour_name']; ?></td>
                                    <td class="text-center small">
                                        <?php echo date('d/m/Y', strtotime($t['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($t['end_date'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill <?php echo ($t['status'] == 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $t['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="3" class="text-center py-4 text-muted small italic">ຍັງບໍ່ມີຂໍ້ມູນການມອບໝາຍວຽກ</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ປິດ</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</main>

<style>
    .btn-white { background: #fff; border: none; }
    .btn-white:hover { background: #f8f9fa; }
</style>

<?php include '../../includes/footer.php'; ?>