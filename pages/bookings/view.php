<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$id = $_GET['id'];
$sql = "SELECT b.*, c.fullname, c.phone, t.tour_name FROM bookings b JOIN customers c ON b.customer_id = c.customer_id JOIN tours t ON b.tour_id = t.tour_id WHERE b.booking_id = $id";
$row = mysqli_fetch_assoc(mysqli_query($conn, $sql));
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center no-print mb-4">
            <div class="d-flex align-items-center">
                <!-- ເພີ່ມປຸ່ມຍ້ອນກັບບ່ອນນີ້ -->
                <a href="index.php" class="btn btn-light rounded-pill px-3 me-3 shadow-sm border">
                    <i class="fas fa-arrow-left me-1"></i> ຍ້ອນກັບ
                </a>
                <h2 class="fw-bold mb-0">ລາຍລະອຽດການຈອງ #BK-<?php echo $id; ?></h2>
            </div>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-print me-1"></i> ພິມໃບບິນ (Print)
            </button>
        </div>

        <div class="card border-0 shadow-lg rounded-4 p-5 bg-white">
            <div class="row mb-5 border-bottom pb-4">
                <div class="col-6">
                    <h2 class="fw-bold text-primary mb-1">Tour Booking</h2>
                    <p class="text-muted">ລະບົບຈັດການການຈອງທົວ</p>
                    <div class="mt-4">
                        <p class="mb-1 text-muted small text-uppercase fw-bold">ຂໍ້ມູນຜູ້ຈອງ:</p>
                        <h5 class="fw-bold mb-0"><?php echo $row['fullname']; ?></h5>
                        <p class="mb-0 text-muted"><?php echo $row['phone']; ?></p>
                    </div>
                </div>
                <div class="col-6 text-end">
                    <div class="mb-4">
                        <?php 
                        $status_label = ($row['status'] == 'Confirmed') ? 'ຢືນຢັນແລ້ວ' : 'ລໍຖ້າອະນຸມັດ';
                        $status_class = ($row['status'] == 'Confirmed') ? 'bg-success' : 'bg-warning text-dark';
                        ?>
                        <span class="badge <?php echo $status_class; ?> px-4 py-2 fs-6 rounded-pill"><?php echo $status_label; ?></span>
                    </div>
                    <p class="mb-1 text-muted">ວັນທີຈອງ: <strong><?php echo date('d/m/Y H:i', strtotime($row['booking_date'])); ?></strong></p>
                    <p class="text-muted">ເລກທີ BK: <strong>#BK-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></strong></p>
                </div>
            </div>

            <div class="mb-5">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-users me-2"></i>ລາຍຊື່ຜູ້ຮ່ວມເດີນທາງທັງໝົດ (<?php echo $row['num_people']; ?> ຄົນ)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th width="60" class="ps-3">#</th>
                                <th>ຊື່ ແລະ ນາມສະກຸນ</th>
                                <th>ເບີໂທຕິດຕໍ່</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3">1</td>
                                <td class="fw-bold"><?php echo $row['fullname']; ?> (ຫົວໜ້າກຸ່ມ)</td>
                                <td><?php echo $row['phone']; ?></td>
                            </tr>
                            <?php 
                            $parts = mysqli_query($conn, "SELECT * FROM booking_participants WHERE booking_id = $id");
                            $i = 2;
                            while($p = mysqli_fetch_assoc($parts)) {
                                echo "<tr>
                                        <td class='ps-3'>$i</td>
                                        <td>".$p['participant_name']."</td>
                                        <td>".$p['participant_phone']."</td>
                                      </tr>";
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <table class="table table-bordered text-center">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="py-3">ລາຍການທົວ (Tour Package)</th>
                        <th class="py-3">ຈຳນວນຄົນ</th>
                        <th class="py-3">ລາຄາລວມ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-4"><strong><?php echo $row['tour_name']; ?></strong></td>
                        <td class="py-4"><?php echo $row['num_people']; ?> ຄົນ</td>
                        <td class="py-4 fw-bold text-danger fs-5"><?php echo number_format($row['total_price']); ?> ກີບ</td>
                    </tr>
                </tbody>
            </table>

            <div class="row mt-5 pt-5 text-center">
                <div class="col-6">
                    <div style="height: 80px;"></div>
                    <p class="mb-0">___________________</p>
                    <p class="small text-muted mt-2">ລາຍເຊັນລູກຄ້າ</p>
                </div>
                <div class="col-6">
                    <div style="height: 80px;"></div>
                    <p class="mb-0">___________________</p>
                    <p class="small text-muted mt-2">ເຈົ້າໜ້າທີ່ແອດມິນ</p>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
@media print {
    .sidebar, .no-print, nav { display: none !important; }
    .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { background: white !important; }
}
</style>
<?php include '../../includes/footer.php'; ?>