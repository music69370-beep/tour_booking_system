<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../config/db.php'; 

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h2 class="fw-bold">
            <i class="fas fa-calendar-check text-primary me-2"></i>ລາຍການຈອງທົວ
        </h2>

        <div class="d-flex gap-3 align-items-center">
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
                        $sql = "SELECT b.*, c.fullname, t.tour_name 
                                FROM bookings b
                                JOIN customers c ON b.customer_id = c.customer_id
                                JOIN tours t ON b.tour_id = t.tour_id";
                        
                        if ($search != '') {
                            $sql .= " WHERE c.fullname LIKE '%$search%' OR t.tour_name LIKE '%$search%' OR b.booking_id LIKE '%$search%'";
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
                                <td><i class="fas fa-map-marker-alt text-danger me-1 small"></i> <?php echo $row['tour_name']; ?></td>
                                <td class="text-center"><?php echo $row['num_people']; ?> ຄົນ</td>
                                <td class="text-end fw-bold text-danger"><?php echo number_format($row['total_price']); ?> ກີບ</td>
                                <td class="text-center">
                                    <?php 
                                    $status = $row['status'];
                                    $badge_class = ($status == 'Confirmed') ? 'bg-success' : (($status == 'Cancelled') ? 'bg-danger' : 'bg-warning text-dark');
                                    ?>
                                    <span class="badge rounded-pill <?php echo $badge_class; ?> px-3 py-2"><?php echo $status; ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill">
                                        <a href="view.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-white text-primary border-end"><i class="fas fa-eye"></i></a>
                                        <a href="javascript:void(0)" 
                                           onclick="confirmDelete(<?php echo $row['booking_id']; ?>, 'delete.php')" 
                                           class="btn btn-sm btn-white text-danger"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">ບໍ່ພົບຂໍ້ມູນການຈອງ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
    .btn-white { background-color: #fff; }
    .btn-white:hover { background-color: #f8f9fa; }
</style>

<?php include '../../includes/footer.php'; ?>