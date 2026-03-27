<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-ticket-alt text-danger me-2"></i>ຈັດການຄູປອງສ່ວນຫຼຸດ</h2>
            <a href="add.php" class="btn btn-danger rounded-pill px-4 shadow-sm">+ ສ້າງຄູປອງໃໝ່</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ລະຫັດຄູປອງ</th>
                            <th>ສ່ວນຫຼຸດ (ກີບ)</th>
                            <th>ວັນໝົດອາຍຸ</th>
                            <th>ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM coupons ORDER BY coupon_id DESC");
                        if(mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)):
                                $is_expired = ($row['expiry_date'] < date('Y-m-d'));
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-primary fs-5"><?php echo $row['code']; ?></span>
                                </td>
                                <td class="fw-bold text-danger">
                                    - <?php echo number_format($row['discount_amount']); ?> ກີບ
                                </td>
                                <td>
                                    <span class="<?php echo $is_expired ? 'text-danger fw-bold' : ''; ?>">
                                        <?php echo date('d/m/Y', strtotime($row['expiry_date'])); ?>
                                        <?php echo $is_expired ? ' (ໝົດອາຍຸ)' : ''; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($row['status'] == 'Active' && !$is_expired): ?>
                                        <span class="badge rounded-pill bg-success px-3 py-2">Active</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-secondary px-3 py-2">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['coupon_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger shadow-sm border rounded-pill">
                                        <i class="fas fa-trash"></i> ລຶບ
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນຄູປອງ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<style> .btn-white { background: #fff; border: none; } .btn-white:hover { background: #f8f9fa; } </style>
<?php include '../../includes/footer.php'; ?>