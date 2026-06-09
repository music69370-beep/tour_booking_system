<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-bus text-info me-2"></i>ລາຍການພາຫະນະທັງໝົດ</h2>
            <a href="add.php" class="btn btn-info text-white rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມລົດໃໝ່
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ຂໍ້ມູນລົດ</th>
                            <th>ປະເພດ</th>
                            <th class="text-center">ບ່ອນນັ່ງ</th>
                            <th>ວັນໝົດປະກັນໄພ</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM vehicles ORDER BY vehicle_id DESC");
                        if(mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)):
                                $st = $row['status'];
                                $badge = ($st == 'Available') ? 'bg-success' : (($st == 'Busy') ? 'bg-warning text-dark' : 'bg-danger');
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?php echo $row['model']; ?></div>
                                    <span class="badge bg-light text-dark border small"><?php echo $row['plate_number']; ?></span>
                                </td>
                                <td><?php echo $row['vehicle_type']; ?></td>
                                <td class="text-center fw-bold text-primary"><?php echo $row['capacity']; ?> ບ່ອນ</td>
                                <td>
                                    <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($row['insurance_expiry'])); ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?php echo $badge; ?> px-3 py-2">
                                        <?php echo ($st == 'Available') ? 'ວ່າງ' : (($st == 'Busy') ? 'ຕິດວຽກ' : 'ສ້ອມແປງ'); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                        <a href="edit.php?id=<?php echo $row['vehicle_id']; ?>" class="btn btn-sm btn-white text-warning border-end" title="ແກ້ໄຂ"><i class="fas fa-edit"></i></a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['vehicle_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger" title="ລຶບ"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນລົດໃນລະບົບ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<style> .btn-white { background: #fff; border: none; } .btn-white:hover { background: #f8f9fa; } </style>
<?php include '../../includes/footer.php'; ?>