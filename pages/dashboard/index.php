<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// 1. ນັບຈຳນວນທົວ
$tours = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tours"))['total'];
// 2. ນັບຈຳນວນລູກຄ້າ
$customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM customers"))['total'];
// 3. ນັບການຈອງທີ່ Pending
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status='Pending'"))['total'];
// 4. ລາຍຮັບລວມທັງໝົດ (Total Revenue)
$revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM payments"))['total'];
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold text-dark">ພາບລວມລະບົບ (Dashboard)</h2>
    </div>
    
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white p-4 rounded-4">
                <h6>ແພັກເກັດທົວທັງໝົດ</h6>
                <h2 class="fw-bold mb-0"><?php echo $tours; ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white p-4 rounded-4">
                <h6>ລູກຄ້າທັງໝົດ</h6>
                <h2 class="fw-bold mb-0"><?php echo $customers; ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark p-4 rounded-4">
                <h6>ລໍຖ້າຢືນຢັນການຈອງ</h6>
                <h2 class="fw-bold mb-0"><?php echo $pending; ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger text-white p-4 rounded-4">
                <h6>ລາຍຮັບລວມ (ກີບ)</h6>
                <h3 class="fw-bold mb-0"><?php echo number_format($revenue); ?></h3>
            </div>
        </div>
    </div>

    <!-- ໂຊລາຍການຈອງຫຼ້າສຸດ -->
    <div class="mt-5">
        <h5 class="fw-bold mb-3">ລາຍການຈອງຫຼ້າສຸດ</h5>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ວັນທີຈອງ</th>
                            <th>ຊື່ລູກຄ້າ</th>
                            <th>ທົວ</th>
                            <th>ລາຄາລວມ</th>
                            <th>ສະຖານະ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = mysqli_query($conn, "SELECT b.*, c.fullname, t.tour_name FROM bookings b JOIN customers c ON b.customer_id=c.customer_id JOIN tours t ON b.tour_id=t.tour_id ORDER BY b.booking_id DESC LIMIT 5");
                        while($row = mysqli_fetch_assoc($res)):
                        ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($row['booking_date'])); ?></td>
                            <td class="fw-bold"><?php echo $row['fullname']; ?></td>
                            <td><?php echo $row['tour_name']; ?></td>
                            <td class="text-danger fw-bold"><?php echo number_format($row['total_price']); ?></td>
                            <td><span class="badge rounded-pill <?php echo ($row['status']=='Confirmed') ? 'bg-success' : 'bg-warning text-dark'; ?>"><?php echo $row['status']; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>