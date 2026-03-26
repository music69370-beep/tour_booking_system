<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold"><i class="fas fa-users text-success me-2"></i>ຈັດການຂໍ້ມູນລູກຄ້າ</h2>
        <a href="add.php" class="btn btn-success rounded-pill px-4 shadow-sm">
            <i class="fas fa-user-plus me-1"></i> ເພີ່ມລູກຄ້າໃໝ່
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th>ຊື່ ແລະ ນາມສະກຸນ</th>
                            <th>ເບີໂທລະສັບ</th>
                            <th>ອີເມວ</th>
                            <th>ທີ່ຢູ່</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM customers ORDER BY customer_id DESC";
                        $result = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                        ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?php echo $row['customer_id']; ?></td>
                                <td class="fw-bold"><?php echo $row['fullname']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['address']; ?></td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill">
                                        <a href="edit.php?id=<?php echo $row['customer_id']; ?>" class="btn btn-sm btn-white text-warning border-end">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" 
                                           onclick="confirmDelete(<?php echo $row['customer_id']; ?>, 'delete.php')" 
                                           class="btn btn-sm btn-white text-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນລູກຄ້າ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
    .btn-white { background-color: #fff; }
    .btn-white:hover { background-color: #f8f9fa; }
</style>

<?php include '../../includes/footer.php'; ?>