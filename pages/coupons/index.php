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
                                <th>ເງື່ອນໄຂ (Min Spend / Limit)</th>
                                <th>ວັນໝົດອາຍຸ</th>
                                <th>ສະຖານະ</th>
                                <th class="text-center">ຈັດການ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $today = date('Y-m-d');
                            $sql = "SELECT c.*, t.tour_name 
                                    FROM coupons c 
                                    LEFT JOIN tours t ON c.specific_tour_id = t.tour_id 
                                    ORDER BY c.coupon_id DESC";
                            $res = mysqli_query($conn, $sql);

                            if(mysqli_num_rows($res) > 0):
                                while($row = mysqli_fetch_assoc($res)):
                                    // Logic ກວດສອບວັນໝົດອາຍຸ
                                    $is_expired = ($row['expiry_date'] < $today);
                                    
                                    // ກຳນົດ Badge ສະຖານະເປັນພາສາລາວ
                                    if ($is_expired) {
                                        $status_label = "ໝົດອາຍຸ";
                                        $status_class = "bg-danger";
                                    } elseif ($row['status'] == 'Active') {
                                        $status_label = "ໃຊ້ງານຢູ່";
                                        $status_class = "bg-success";
                                    } else {
                                        $status_label = "ປິດໃຊ້ງານ";
                                        $status_class = "bg-secondary";
                                    }
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-primary fs-5"><?php echo $row['code']; ?></div>
                                    <small class="text-muted">ປະເພດ: <?php echo ($row['discount_type'] == 'Percent') ? 'ເປີເຊັນ (%)' : 'ເງິນສົດ'; ?></small>
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
                                    <div class="small">ຊື້ຂັ້ນຕ່ຳ: <strong><?php echo number_format($row['min_spend']); ?></strong> ກີບ</div>
                                    <div class="small">ສິດ: <strong><?php echo $row['total_limit'] ?: 'ບໍ່ຈຳກັດ'; ?></strong> | ໃຊ້ໄດ້ <strong><?php echo $row['limit_per_user']; ?></strong> ຄັ້ງ/ຄົນ</div>
                                </td>
                                <td>
                                    <span class="<?php echo $is_expired ? 'text-danger fw-bold' : ''; ?>">
                                        <i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($row['expiry_date'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill <?php echo $status_class; ?> px-3 py-2">
                                        <?php echo $status_label; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['coupon_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger shadow-sm border rounded-pill">
                                        <i class="fas fa-trash"></i> ລຶບ
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນຄູປອງ</td></tr>
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