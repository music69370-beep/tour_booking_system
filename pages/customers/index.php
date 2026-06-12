<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// ຮັບຄ່າການຄົ້ນຫາ (ຖ້າມີ)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <!-- ສ່ວນຫົວ ແລະ ປຸ່ມເພີ່ມ -->
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-users text-success me-2"></i>ຈັດການຂໍ້ມູນລູກຄ້າ</h2>
            <div class="d-flex gap-2">
                <form action="" method="GET" class="input-group shadow-sm rounded-pill overflow-hidden border">
                    <input type="text" name="search" class="form-control border-0 px-3 shadow-none" placeholder="ຄົ້ນຫາຊື່ ຫຼື ເບີໂທ..." value="<?php echo $search; ?>">
                    <button class="btn btn-white bg-white border-0" type="submit"><i class="fas fa-search text-muted"></i></button>
                </form>
                <a href="add.php" class="btn btn-success rounded-pill px-4 shadow-sm">
                    <i class="fas fa-user-plus me-1"></i> ເພີ່ມລູກຄ້າໃໝ່
                </a>
            </div>
        </div>

        <!-- ຕາຕະລາງຂໍ້ມູນ -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3" width="80">ID</th>
                            <th>ຂໍ້ມູນລູກຄ້າ</th>
                            <th>ການຕິດຕໍ່</th>
                            <th>ເອກະສານຢັ້ງຢືນ</th>
                            <th>ຕິດຕໍ່ສຸກເສີນ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM customers";
                        if ($search != '') {
                            $sql .= " WHERE fullname LIKE '%$search%' OR phone LIKE '%$search%' OR id_card_no LIKE '%$search%'";
                        }
                        $sql .= " ORDER BY customer_id DESC";
                        
                        $result = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                                $gender_label = ($row['gender'] == 'Male') ? 'ຊາຍ' : 'ຍິງ';
                                $gender_color = ($row['gender'] == 'Male') ? 'text-primary' : 'text-danger';
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border font-monospace">#<?php echo str_pad($row['customer_id'], 3, '0', STR_PAD_LEFT); ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6"><?php echo $row['fullname']; ?></div>
                                    <div class="small">
                                        <span class="<?php echo $gender_color; ?> fw-bold"><?php echo $gender_label; ?></span> 
                                        <span class="text-muted mx-1">|</span>
                                        <span class="text-muted">ສັນຊາດ: <?php echo $row['nationality']; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small mb-1"><i class="fas fa-phone-alt me-2 text-success"></i><?php echo $row['phone']; ?></div>
                                    <div class="small text-muted"><i class="fas fa-envelope me-2 text-info"></i><?php echo $row['email'] ?: '---'; ?></div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark"><i class="fas fa-id-card me-2 text-muted"></i><?php echo $row['id_card_no'] ?: 'ບໍ່ມີຂໍ້ມູນ'; ?></div>
                                    <?php if(!empty($row['id_card_image'])): ?>
                                        <a href="../../assets/uploads/customers/<?php echo $row['id_card_image']; ?>" target="_blank" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none mt-1">
                                            <i class="fas fa-eye me-1"></i> ເບິ່ງຮູບເອກະສານ
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($row['emergency_name'])): ?>
                                        <div class="small fw-bold text-danger"><?php echo $row['emergency_name']; ?></div>
                                        <div class="small text-muted"><?php echo $row['emergency_phone']; ?></div>
                                    <?php else: ?>
                                        <span class="text-muted small italic">ບໍ່ມີຂໍ້ມູນ</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill overflow-hidden bg-white">
                                        <a href="edit.php?id=<?php echo $row['customer_id']; ?>" class="btn btn-sm btn-white text-warning border-end px-3" title="ແກ້ໄຂ">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['customer_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger px-3" title="ລຶບ">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ບໍ່ພົບຂໍ້ມູນລູກຄ້າ</td></tr>
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
    .bg-primary-subtle { background-color: #e7f1ff; }
    .bg-light { background-color: #f8f9fa !important; }
    .table-hover tbody tr:hover { background-color: #fcfcfc; }
    .font-monospace { font-family: 'Courier New', Courier, monospace; }
</style>

<?php include '../../includes/footer.php'; ?>