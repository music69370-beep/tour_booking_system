<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-ticket-alt text-danger me-2"></i>ຈັດການຄູປອງສ່ວນຫຼຸດ (Advanced)</h2>
            <a href="add.php" class="btn btn-danger rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> ສ້າງຄູປອງໃໝ່
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">ລະຫັດ / ປະເພດ</th>
                                <th>ມູນຄ່າສ່ວນຫຼຸດ</th>
                                <th>ເງື່ອນໄຂການໃຊ້ (Min / Limit)</th>
                                <th>ວັນໝົດອາຍຸ</th>
                                <th>ສະຖານະ</th>
                                <th class="text-center">ຈັດການ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // ດຶງຂໍ້ມູນຄູປອງ ພ້ອມ Join ຊື່ທົວ (ຖ້າມີການກຳນົດສະເພາະທົວ)
                            $sql = "SELECT c.*, t.tour_name 
                                    FROM coupons c 
                                    LEFT JOIN tours t ON c.specific_tour_id = t.tour_id 
                                    ORDER BY c.coupon_id DESC";
                            $res = mysqli_query($conn, $sql);

                            if(mysqli_num_rows($res) > 0):
                                while($row = mysqli_fetch_assoc($res)):
                                    $is_expired = ($row['expiry_date'] < date('Y-m-d'));
                                    
                                    // ນັບວ່າຄູປອງນີ້ຖືກໃຊ້ໄປຈັກຄັ້ງແລ້ວ
                                    $cid = $row['coupon_id'];
                                    $used_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE coupon_id = $cid AND status != 'Cancelled'");
                                    $used_count = mysqli_fetch_assoc($used_res)['total'];
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-primary fs-5"><?php echo $row['code']; ?></div>
                                    <span class="badge bg-light text-dark border small fw-normal">
                                        Type: <?php echo ($row['discount_type'] == 'Percent') ? 'ເປີເຊັນ (%)' : 'ເງິນສົດ (LAK)'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-danger fs-5">
                                        <?php 
                                        if ($row['discount_type'] == 'Percent') {
                                            echo $row['discount_value'] . " %";
                                            if ($row['max_discount'] > 0) {
                                                echo "<div class='small text-muted' style='font-size: 0.7rem;'>(ຫຼຸດສູງສຸດ ".number_format($row['max_discount'])." ກີບ)</div>";
                                            }
                                        } else {
                                            echo "- " . number_format($row['discount_value']) . " ກີບ";
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small mb-1">ຊື້ຂັ້ນຕ່ຳ: <strong><?php echo number_format($row['min_spend']); ?></strong> ກີບ</div>
                                    <div class="small">
                                        ສິດການໃຊ້: <strong class="text-primary"><?php echo $used_count; ?></strong> / <?php echo ($row['total_limit'] > 0) ? $row['total_limit'] : 'ບໍ່ຈຳກັດ'; ?>
                                    </div>
                                    <?php if($row['tour_name']): ?>
                                        <div class="small text-info mt-1"><i class="fas fa-thumbtack me-1"></i>ສະເພາະ: <?php echo $row['tour_name']; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small <?php echo $is_expired ? 'text-danger fw-bold' : 'text-dark'; ?>">
                                        <i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($row['expiry_date'])); ?>
                                        <?php if($is_expired) echo "<br><span class='badge bg-danger' style='font-size:0.6rem'>EXPIRED</span>"; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($row['status'] == 'Active' && !$is_expired): ?>
                                        <span class="badge rounded-pill bg-success px-3 py-2">Active</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-secondary px-3 py-2">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['coupon_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger px-3">
                                            <i class="fas fa-trash"></i> ລຶບ
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນຄູປອງສ່ວນຫຼຸດ</td></tr>
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
    .table thead th { font-size: 0.75rem; letter-spacing: 0.5px; }
</style>

<?php include '../../includes/footer.php'; ?>