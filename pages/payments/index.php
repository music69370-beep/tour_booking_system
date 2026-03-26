<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold"><i class="fas fa-money-bill-wave text-success me-2"></i>ປະຫວັດການຊຳລະເງິນ</h2>
        <a href="add.php" class="btn btn-success rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> ບັນທຶກການຮັບເງິນ
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">ID ໃບບິນ</th>
                            <th>ID ການຈອງ</th>
                            <th>ຊື່ລູກຄ້າ</th>
                            <th>ຈຳນວນເງິນ</th>
                            <th>ຊ່ອງທາງ</th>
                            <th>ວັນທີຊຳລະ</th>
                            <th class="text-center">ໃບບິນ (Slip)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT p.*, c.fullname, b.booking_id 
                                FROM payments p
                                JOIN bookings b ON p.booking_id = b.booking_id
                                JOIN customers c ON b.customer_id = c.customer_id
                                ORDER BY p.payment_id DESC";
                        $result = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                        ?>
                            <tr>
                                <td class="ps-4">#PAY-<?php echo $row['payment_id']; ?></td>
                                <td>#BK-<?php echo $row['booking_id']; ?></td>
                                <td class="fw-bold"><?php echo $row['fullname']; ?></td>
                                <td class="text-success fw-bold"><?php echo number_format($row['amount']); ?> ກີບ</td>
                                <td><span class="badge bg-info text-dark"><?php echo $row['payment_method']; ?></span></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['payment_date'])); ?></td>
                                <td class="text-center">
                                    <a href="../../assets/uploads/payments/<?php echo $row['payment_slip']; ?>" target="_blank">
                                        <img src="../../assets/uploads/payments/<?php echo $row['payment_slip']; ?>" class="rounded shadow-sm" width="50">
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">ຍັງບໍ່ມີປະຫວັດການຊຳລະເງິນ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>