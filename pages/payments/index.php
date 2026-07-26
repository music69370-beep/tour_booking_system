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
            <h2 class="fw-bold"><i class="fas fa-money-bill-wave text-success me-2"></i>ປະຫວັດການຊຳລະເງິນ</h2>
            <a href="add.php" class="btn btn-success rounded-pill px-4 shadow-sm">+ ບັນທຶກການຮັບເງິນ</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ID ໃບບິນ</th>
                            <th>ຊື່ລູກຄ້າ / BK-ID</th>
                            <th>ຈຳນວນເງິນ</th>
                            <th>ຊ່ອງທາງ / ຜູ້ຮັບເງິນ</th>
                            <th>ວັນທີຊຳລະ</th>
                            <th class="text-center">ໃບບິນ (Slip)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // ປັບ SQL ໃຫ້ JOIN ເອົາຊື່ຜູ້ຮັບເງິນ (received_by)
                        $sql = "SELECT p.*, c.fullname, b.booking_id, u.fullname as receiver_name 
                                FROM payments p
                                JOIN bookings b ON p.booking_id = b.booking_id
                                JOIN customers c ON b.customer_id = c.customer_id
                                LEFT JOIN users u ON p.received_by = u.user_id
                                ORDER BY p.payment_id DESC";
                        $result = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                        ?>
                            <tr>
                                <td class="ps-4">#PAY-<?php echo $row['payment_id']; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo $row['fullname']; ?></div>
                                    <small class="text-muted">Booking: #BK-<?php echo $row['booking_id']; ?></small>
                                </td>
                                <td class="text-success fw-bold"><?php echo number_format($row['amount']); ?> ກີບ</td>
                                <td>
                                    <span class="badge bg-info text-dark mb-1 d-block" style="width: fit-content;"><?php echo $row['payment_method']; ?></span>
                                    <small class="text-muted"><i class="fas fa-user-check me-1"></i> <?php echo $row['receiver_name'] ?? 'Online/Auto'; ?></small>
                                </td>
                                <td class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($row['payment_date'])); ?></td>
                                <td class="text-center">
                                    <?php if($row['payment_slip']): ?>
                                        <a href="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['payment_slip']; ?>" target="_blank">
                                            <img src="<?php echo BASE_URL; ?>assets/uploads/payments/<?php echo $row['payment_slip']; ?>" class="rounded border" width="40" height="40" style="object-fit: cover;">
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີປະຫວັດການຊຳລະເງິນ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>