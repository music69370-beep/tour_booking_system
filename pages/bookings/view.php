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
        <div class="d-flex justify-content-between no-print mb-4">
            <h2 class="fw-bold">ລາຍລະອຽດການຈອງ #BK-<?php echo $id; ?></h2>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4"><i class="fas fa-print"></i> ພິມໃບບິນ</button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-5 bg-white">
            <div class="row mb-5 border-bottom pb-4">
                <div class="col-6">
                    <h5 class="fw-bold text-primary">Tour Booking System</h5>
                    <p class="mb-0">ຜູ້ຈອງ: <strong><?php echo $row['fullname']; ?></strong></p>
                    <p class="mb-0">ເບີໂທ: <?php echo $row['phone']; ?></p>
                </div>
                <div class="col-6 text-end">
                    <h6 class="text-muted small">ວັນທີຈອງ: <?php echo date('d/m/Y H:i', strtotime($row['booking_date'])); ?></h6>
                    <h4 class="fw-bold text-success">ສະຖານະ: <?php echo $row['status']; ?></h4>
                </div>
            </div>

            <!-- ໂຊລາຍຊື່ ແລະ ເບີໂທ ທຸກຄົນ -->
            <div class="mb-5">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-users me-2"></i>ລາຍຊື່ຜູ້ຮ່ວມເດີນທາງທັງໝົດ (<?php echo $row['num_people']; ?> ຄົນ)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">#</th>
                                <th>ຊື່ ແລະ ນາມສະກຸນ</th>
                                <th>ເບີໂທຕິດຕໍ່</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td class="fw-bold"><?php echo $row['fullname']; ?> (ຫົວໜ້າກຸ່ມ)</td>
                                <td><?php echo $row['phone']; ?></td>
                            </tr>
                            <?php 
                            $parts = mysqli_query($conn, "SELECT * FROM booking_participants WHERE booking_id = $id");
                            $i = 2;
                            while($p = mysqli_fetch_assoc($parts)) {
                                echo "<tr>
                                        <td>$i</td>
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
                <thead class="bg-light text-muted small">
                    <tr>
                        <th>ລາຍການທົວ (Tour Package)</th>
                        <th>ຈຳນວນ</th>
                        <th>ລາຄາລວມ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-3"><strong><?php echo $row['tour_name']; ?></strong></td>
                        <td><?php echo $row['num_people']; ?> ຄົນ</td>
                        <td class="fw-bold text-danger"><?php echo number_format($row['total_price']); ?> ກີບ</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>