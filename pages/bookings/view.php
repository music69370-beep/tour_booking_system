<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

if (!isset($_GET['id'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. ດຶງຂໍ້ມູນການຈອງ ແລະ ລູກຄ້າຫຼັກ
$sql = "SELECT b.*, c.fullname, c.phone, t.tour_name 
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        WHERE b.booking_id = '$id'";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);

if (!$row) {
    echo "<div class='p-5 text-center'><h3>ບໍ່ພົບຂໍ້ມູນການຈອງ</h3><a href='index.php'>ກັບຄືນ</a></div>";
    exit;
}

// ແຍກ Array ບ່ອນນັ່ງທັງໝົດອອກມາເພື່ອໃຊ້ເປັນບ່ອນອີງ
$all_seats = !empty($row['selected_seats']) ? explode(',', $row['selected_seats']) : [];
?>

<style>
    .seat-badge-fixed {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-weight: 800;
        font-size: 1.2rem;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);
    }
    .table-custom thead { background-color: #f8f9fa; }
    .table-custom th { font-size: 0.85rem; text-transform: uppercase; color: #6c757d; border: none; }
    .table-custom td { vertical-align: middle; border-bottom: 1px solid #f1f3f7; padding: 15px 10px; }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">ລາຍລະອຽດການຈອງ #BK-<?php echo $id; ?></h2>
            <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-4 no-print">
                <i class="fas fa-print me-2"></i> ພິມລາຍຊື່
            </button>
        </div>

        <div class="row g-4">
            <!-- ເບື້ອງຊ້າຍ: ຂໍ້ມູນທົ່ວໄປ -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fas fa-info-circle me-2"></i>ຂໍ້ມູນທົ່ວໄປ</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">ລູກຄ້າຜູ້ຈອງ:</small>
                            <span class="fw-bold fs-5"><?php echo $row['fullname']; ?></span>
                            <small class="d-block text-primary"><?php echo $row['phone']; ?></small>
                        </div>
                        <div class="col-md-6 mb-3 text-md-end">
                            <small class="text-muted d-block">ແພັກເກັດທົວ:</small>
                            <span class="fw-bold text-dark"><?php echo $row['tour_name']; ?></span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">ວັນທີເດີນທາງ:</small>
                            <span class="fw-bold"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></span>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small class="text-muted d-block">ຈຳນວນຜູ້ເດີນທາງ:</small>
                            <span class="badge bg-light text-dark border px-3"><?php echo $row['num_people']; ?> ທ່ານ</span>
                        </div>
                    </div>
                </div>

                <!-- ຕາຕະລາງລະບຸບ່ອນນັ່ງລາຍຄົນ (Passenger List with Assigned Seats) -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-users text-primary me-2"></i>ລາຍຊື່ຜູ້ໂດຍສານ ແລະ ບ່ອນນັ່ງ</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">ລຳດັບ</th>
                                    <th>ຊື່ ແລະ ນາມສະກຸນ</th>
                                    <th class="text-center">ເບີບ່ອນນັ່ງ</th>
                                    <th class="text-center">ສະຖານະ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 1. ລູກຄ້າຫຼັກ -->
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">01</td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo $row['fullname']; ?></div>
                                        <small class="badge bg-success-subtle text-success small">ລູກຄ້າຫຼັກ (Lead)</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="badge bg-primary seat-badge-fixed mx-auto">
                                            <?php echo isset($all_seats[0]) ? $all_seats[0] : '-'; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-success small"><i class="fas fa-check-circle me-1"></i> ຢືນຢັນແລ້ວ</span>
                                    </td>
                                </tr>

                                <!-- 2. ຜູ້ຮ່ວມທາງ (Participants) -->
                                <?php 
                                $p_sql = "SELECT * FROM booking_participants WHERE booking_id = '$id' ORDER BY part_id ASC";
                                $p_res = mysqli_query($conn, $p_sql);
                                $i = 2;
                                while($p = mysqli_fetch_assoc($p_res)): 
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></td>
                                    <td>
                                        <div class="text-dark"><?php echo $p['participant_name']; ?></div>
                                        <small class="text-muted"><?php echo $p['participant_phone'] ?: 'ບໍ່ມີເບີໂທ'; ?></small>
                                    </td>
                                    <td class="text-center">
                                        <div class="badge bg-primary seat-badge-fixed mx-auto">
                                            <?php 
                                                // ຖ້າມີການເກັບ seat ໃນຕາຕະລາງ participant ໃຫ້ເອົາມາໂຊ, ຖ້າບໍ່ມີໃຫ້ໄລ່ຕາມ index ຂອງ all_seats
                                                echo !empty($p['participant_seat']) ? $p['participant_seat'] : (isset($all_seats[$i-1]) ? $all_seats[$i-1] : '-'); 
                                            ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-success small"><i class="fas fa-check-circle me-1"></i> ຢືນຢັນແລ້ວ</span>
                                    </td>
                                </tr>
                                <?php $i++; endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ເບື້ອງຂວາ: ສະຫຼຸບການເງິນ -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark text-white text-center mb-4">
                    <h6 class="opacity-75 small text-uppercase fw-bold mb-3">ຍອດເງິນທີ່ຊຳລະແລ້ວ</h6>
                    <h2 class="fw-bold text-success display-6 mb-3">₭ <?php echo number_format($row['total_price']); ?></h2>
                    
                    <?php 
                        $status = $row['status'];
                        $st_class = ($status == 'Confirmed') ? 'bg-success' : (($status == 'Cancelled') ? 'bg-danger' : 'bg-warning text-dark');
                    ?>
                    <div class="badge <?php echo $st_class; ?> px-4 py-2 rounded-pill fs-6 shadow-sm">
                        <i class="fas fa-circle me-1 small"></i> <?php echo $status; ?>
                    </div>
                </div>

                <!-- ໝາຍເຫດ -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">ໝາຍເຫດການຈອງ</h6>
                    <p class="small text-muted mb-0">
                        <?php echo !empty($row['note']) ? nl2br($row['note']) : 'ບໍ່ມີໝາຍເຫດເພີ່ມເຕີມ'; ?>
                    </p>
                </div>
                
                <div class="mt-4 no-print">
                    <a href="index.php" class="btn btn-light w-100 rounded-pill border fw-bold text-muted shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i> ກັບຄືນລາຍການຈອງ
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    @media print {
        .no-print, .sidebar, nav { display: none !important; }
        .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
    }
</style>

<?php include '../../includes/footer.php'; ?>