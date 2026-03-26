<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold"><i class="fas fa-map-marked-alt text-primary me-2"></i>ລາຍການແພັກເກັດທົວ</h2>
            <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມທົວໃໝ່
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">ຮູບພາບ</th>
                                <th>ຊື່ແພັກເກັດທົວ</th>
                                <th>ບ່ອນນັ່ງ (ຫວ່າງ)</th>
                                <th>ລາຄາ (ກີບ)</th>
                                <th>ສະຖານະ</th>
                                <th class="text-center">ຈັດການ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM tours ORDER BY tour_id DESC";
                            $result = mysqli_query($conn, $sql);
                            
                            if(mysqli_num_rows($result) > 0):
                                while($row = mysqli_fetch_assoc($result)):
                                    // ຄຳນວນບ່ອນນັ່ງຫວ່າງ
                                    $tid = $row['tour_id'];
                                    $booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
                                    $booked_count = $booked_res['total'] ?? 0;
                                    $remaining = $row['max_seats'] - $booked_count;
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <img src="<?php echo BASE_URL; ?>assets/uploads/tours/<?php echo $row['image']; ?>" 
                                             class="rounded-3 border shadow-sm" width="80" height="50" style="object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo $row['tour_name']; ?></div>
                                        <small class="text-muted"><i class="far fa-clock me-1"></i> <?php echo $row['duration']; ?></small>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo ($remaining <= 2) ? 'bg-danger' : 'bg-info text-dark'; ?> rounded-pill">
                                            <?php echo $remaining; ?> / <?php echo $row['max_seats']; ?> ບ່ອນ
                                        </span>
                                    </td>
                                    <td class="text-danger fw-bold"><?php echo number_format($row['price']); ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo ($row['status'] == 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                            <a href="edit.php?id=<?php echo $row['tour_id']; ?>" class="btn btn-sm btn-white text-warning border-end">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" 
                                               onclick="confirmDelete(<?php echo $row['tour_id']; ?>, 'delete.php')" 
                                               class="btn btn-sm btn-white text-danger">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນແພັກເກັດທົວ</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<style> .btn-white { background: #fff; border: none; } .btn-white:hover { background: #f8f9fa; } </style>
<?php include '../../includes/footer.php'; ?>