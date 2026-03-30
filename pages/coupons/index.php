<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-ticket-alt text-danger me-2"></i>ຈັດການຄູປອງສ່ວນຫຼຸດ</h2>
            <a href="add.php" class="btn btn-danger rounded-pill px-4 shadow-sm">+ ສ້າງຄູປອງໃໝ່</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ລະຫັດ / ປະເພດ</th>
                            <th>ມູນຄ່າສ່ວນຫຼຸດ</th>
                            <th>ເງື່ອນໄຂ (Min / Limit)</th>
                            <th>ວັນໝົດອາຍຸ</th>
                            <th>ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $today = date('Y-m-d');
                        $sql = "SELECT c.* FROM coupons c ORDER BY c.coupon_id DESC";
                        $res = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)):
                                $is_expired = ($row['expiry_date'] < $today);
                                $cid = $row['coupon_id'];
                                
                                // --- Logic ນັບການໃຊ້ງານທີ່ຖືກຕ້ອງ ---
                                $count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE coupon_id = $cid AND status != 'Cancelled'");
                                $used_count = mysqli_fetch_assoc($count_res)['total'];

                                if ($is_expired) { $st_label = "ໝົດອາຍຸ"; $st_class = "bg-danger"; }
                                elseif ($row['status'] == 'Active') { $st_label = "ໃຊ້ງານຢູ່"; $st_class = "bg-success"; }
                                else { $st_label = "ປິດໃຊ້ງານ"; $st_class = "bg-secondary"; }
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-primary fs-5"><?php echo $row['code']; ?></div>
                                    <small class="text-muted">Type: <?php echo $row['discount_type']; ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold text-danger">
                                        <?php echo ($row['discount_type'] == 'Percent') ? $row['discount_value']." %" : number_format($row['discount_value'])." ກີບ"; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">ຊື້ຂັ້ນຕ່ຳ: <strong><?php echo number_format($row['min_spend']); ?></strong></div>
                                    <div class="small">ໃຊ້ແລ້ວ: <strong class="text-primary"><?php echo $used_count; ?></strong> / <?php echo $row['total_limit'] ?: '∞'; ?></div>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($row['expiry_date'])); ?></td>
                                <td><span class="badge rounded-pill <?php echo $st_class; ?> px-3 py-2"><?php echo $st_label; ?></span></td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['coupon_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger border rounded-pill shadow-sm"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>