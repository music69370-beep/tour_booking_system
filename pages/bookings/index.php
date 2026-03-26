<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold"><i class="fas fa-calendar-check text-primary me-2"></i>ລາຍການຈອງທົວທັງໝົດ</h2>
        <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> ສ້າງການຈອງໃໝ່
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th>ຊື່ລູກຄ້າ</th>
                            <th>ແພັກເກັດທົວ</th>
                            <th>ຈຳນວນຄົນ</th>
                            <th>ລາຄາລວມ</th>
                            <th>ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT b.*, c.fullname, t.tour_name 
                                FROM bookings b
                                JOIN customers c ON b.customer_id = c.customer_id
                                JOIN tours t ON b.tour_id = t.tour_id
                                ORDER BY b.booking_id DESC";
                        $result = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                        ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?php echo $row['booking_id']; ?></td>
                                <td class="fw-bold"><?php echo $row['fullname']; ?></td>
                                <td><?php echo $row['tour_name']; ?></td>
                                <td><?php echo $row['num_people']; ?> ຄົນ</td>
                                <td class="text-danger fw-bold"><?php echo number_format($row['total_price']); ?> ກີບ</td>
                                <td>
                                    <span class="badge rounded-pill <?php 
                                        echo ($row['status'] == 'Pending') ? 'bg-warning text-dark' : (($row['status'] == 'Confirmed') ? 'bg-success' : 'bg-danger'); 
                                    ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="view.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill">ເບິ່ງ</a>
                                    <a href="delete.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('ຢືນຢັນການລຶບ?')">ລຶບ</a>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">ຍັງບໍ່ມີລາຍການຈອງ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>