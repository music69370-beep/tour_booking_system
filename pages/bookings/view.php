<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$id = $_GET['id'];
// Join ຂໍ້ມູນໃຫ້ຄົບ: Bookings + Customers + Tours + Payments
$sql = "SELECT b.*, c.fullname, c.phone, c.address, t.tour_name, t.duration, p.payment_method, p.payment_date
        FROM bookings b
        JOIN customers c ON b.customer_id = c.customer_id
        JOIN tours t ON b.tour_id = t.tour_id
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        WHERE b.booking_id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) { echo "ບໍ່ພົບຂໍ້ມູນ"; exit; }
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom no-print">
        <h2 class="fw-bold">ລາຍລະອຽດການຈອງ #BK-<?php echo $id; ?></h2>
        <div>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-print me-1"></i> ພິມໃບບິນ (Print)
            </button>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 ms-2">ກັບຄືນ</a>
        </div>
    </div>

    <!-- ສ່ວນທີ່ຈະພິມອອກມາ -->
    <div class="card border-0 shadow-sm rounded-4 p-5 bg-white mb-5" id="printableArea">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-primary">Tour Booking System</h1>
            <p class="text-muted">ໃບຢັ້ງຢືນການຈອງທົວ (Booking Voucher)</p>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <h5 class="fw-bold border-bottom pb-2">ຂໍ້ມູນລູກຄ້າ</h5>
                <p class="mb-1"><strong>ຊື່:</strong> <?php echo $row['fullname']; ?></p>
                <p class="mb-1"><strong>ເບີໂທ:</strong> <?php echo $row['phone']; ?></p>
                <p class="mb-1"><strong>ທີ່ຢູ່:</strong> <?php echo $row['address']; ?></p>
            </div>
            <div class="col-6 text-end">
                <h5 class="fw-bold border-bottom pb-2">ລາຍລະອຽດການຈອງ</h5>
                <p class="mb-1"><strong>ເລກທີຈອງ:</strong> #BK-<?php echo $id; ?></p>
                <p class="mb-1"><strong>ວັນທີຈອງ:</strong> <?php echo date('d/m/Y', strtotime($row['booking_date'])); ?></p>
                <p class="mb-1"><strong>ສະຖານະ:</strong> 
                    <span class="badge <?php echo ($row['status']=='Confirmed')?'bg-success':'bg-warning text-dark'; ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </p>
            </div>
        </div>

        <table class="table table-bordered mt-4">
            <thead class="bg-light">
                <tr>
                    <th>ລາຍການທົວ (Tour Package)</th>
                    <th class="text-center">ໄລຍະເວລາ</th>
                    <th class="text-center">ຈຳນວນຄົນ</th>
                    <th class="text-end">ລາຄາລວມ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-3"><strong><?php echo $row['tour_name']; ?></strong></td>
                    <td class="text-center"><?php echo $row['duration']; ?></td>
                    <td class="text-center"><?php echo $row['num_people']; ?> ຄົນ</td>
                    <td class="text-end fw-bold text-danger"><?php echo number_format($row['total_price']); ?> ກີບ</td>
                </tr>
            </tbody>
        </table>

        <?php if ($row['status'] == 'Confirmed'): ?>
        <div class="alert alert-success mt-4 border-0">
            <i class="fas fa-check-circle me-2"></i> 
            ການຊຳລະເງິນສຳເລັດແລ້ວ ຜ່ານທາງ <strong><?php echo $row['payment_method']; ?></strong> 
            ເມື່ອວັນທີ <?php echo date('d/m/Y H:i', strtotime($row['payment_date'])); ?>
        </div>
        <?php endif; ?>

        <div class="row mt-5 pt-5 text-center">
            <div class="col-6">
                <p class="mb-5">ລາຍເຊັນລູກຄ້າ</p>
                <p>___________________</p>
            </div>
            <div class="col-6">
                <p class="mb-5">ເຈົ້າໜ້າທີ່ແອດມິນ</p>
                <p>___________________</p>
            </div>
        </div>
    </div>
</main>

<style>
/* CSS ສ້າງຂຶ້ນມາເພື່ອໃຊ້ຕອນສັ່ງ Print */
@media print {
    .sidebar, .no-print, nav { display: none !important; }
    .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { background: white !important; }
}
</style>

<?php include '../../includes/footer.php'; ?>