<?php 
// 1. ດຶງໄຟລ໌ເຊື່ອມຕໍ່ ແລະ ຕັ້ງຄ່າ Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../config/db.php'; 

// 2. ຮັບຄ່າຄົ້ນຫາ (Search Logic)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <!-- ສ່ວນຫົວຂອງໜ້າ -->
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h2 class="fw-bold">
            <i class="fas fa-calendar-check text-primary me-2"></i>ລາຍການຈອງທົວ
        </h2>

        <div class="d-flex gap-3 align-items-center">
            <!-- ຟອມຄົ້ນຫາ (Search Form) -->
            <form action="" method="GET" class="d-flex shadow-sm rounded-pill overflow-hidden border">
                <input type="text" name="search" class="form-control border-0 px-3 py-2 shadow-none" 
                       placeholder="ຄົ້ນຫາຊື່ລູກຄ້າ ຫຼື ທົວ..." 
                       value="<?php echo htmlspecialchars($search); ?>" style="width: 250px;">
                <button type="submit" class="btn btn-white bg-white border-0 px-3">
                    <i class="fas fa-search text-muted"></i>
                </button>
            </form>

            <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> ສ້າງການຈອງໃໝ່
            </a>
        </div>
    </div>

    <!-- ສະແດງຂໍ້ຄວາມແຈ້ງເຕືອນເມື່ອຄົ້ນຫາ -->
    <?php if ($search != ''): ?>
        <div class="mb-3">
            <span class="text-muted">ຜົນການຄົ້ນຫາສຳລັບ: </span>
            <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                "<?php echo htmlspecialchars($search); ?>"
                <a href="index.php" class="text-danger ms-2 text-decoration-none"><i class="fas fa-times-circle"></i></a>
            </span>
        </div>
    <?php endif; ?>

    <!-- ຕາຕະລາງສະແດງຂໍ້ມູນ -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr class="text-muted small text-uppercase">
                            <th class="ps-4 py-3">ID ການຈອງ</th>
                            <th>ລູກຄ້າ</th>
                            <th>ແພັກເກັດທົວ</th>
                            <th class="text-center">ຈຳນວນຄົນ</th>
                            <th class="text-end">ລາຄາລວມ</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 3. SQL Query ພ້ອມເງື່ອນໄຂຄົ້ນຫາ
                        $sql = "SELECT b.*, c.fullname, t.tour_name 
                                FROM bookings b
                                JOIN customers c ON b.customer_id = c.customer_id
                                JOIN tours t ON b.tour_id = t.tour_id";
                        
                        if ($search != '') {
                            $sql .= " WHERE c.fullname LIKE '%$search%' 
                                      OR t.tour_name LIKE '%$search%' 
                                      OR b.booking_id LIKE '%$search%'";
                        }
                        
                        $sql .= " ORDER BY b.booking_id DESC";
                        $result = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                        ?>
                            <tr>
                                <td class="ps-4 text-muted small">#BK-<?php echo str_pad($row['booking_id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $row['fullname']; ?></div>
                                    <small class="text-muted"><?php echo date('d/m/Y', strtotime($row['booking_date'])); ?></small>
                                </td>
                                <td>
                                    <span class="text-dark"><i class="fas fa-map-marker-alt text-danger me-1 small"></i> <?php echo $row['tour_name']; ?></span>
                                </td>
                                <td class="text-center"><?php echo $row['num_people']; ?> ຄົນ</td>
                                <td class="text-end fw-bold text-danger">
                                    <?php echo number_format($row['total_price']); ?> ກີບ
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $status = $row['status'];
                                    $badge_class = 'bg-warning text-dark'; // Pending
                                    if ($status == 'Confirmed') $badge_class = 'bg-success';
                                    if ($status == 'Cancelled') $badge_class = 'bg-danger';
                                    ?>
                                    <span class="badge rounded-pill <?php echo $badge_class; ?> px-3 py-2" style="font-weight: 500;">
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill">
                                        <a href="view.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-white text-primary border-end" title="ເບິ່ງລາຍລະອຽດ">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $row['booking_id']; ?>" 
                                           class="btn btn-sm btn-white text-danger" 
                                           title="ລຶບ" 
                                           onclick="return confirm('ທ່ານແນ່ໃຈບໍ່ວ່າຈະລຶບການຈອງນີ້?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="fas fa-search fa-3x mb-3 text-muted opacity-25"></i>
                                        <h5 class="text-muted">ບໍ່ພົບຂໍ້ມູນການຈອງ</h5>
                                        <p class="small text-secondary">ລອງຄົ້ນຫາດ້ວຍຄຳສັບອື່ນ ຫຼື ສ້າງການຈອງໃໝ່</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
    /* CSS ເພີ່ມເຕີມເພື່ອຄວາມສວຍງາມ */
    .main-content { background-color: #f8f9fa; }
    .btn-white { background-color: #fff; }
    .btn-white:hover { background-color: #f8f9fa; }
    .table thead th { font-weight: 600; letter-spacing: 0.5px; }
    .card { border: 1px solid rgba(0,0,0,0.05) !important; }
    .badge { min-width: 90px; }
</style>

<?php include '../../includes/footer.php'; ?>