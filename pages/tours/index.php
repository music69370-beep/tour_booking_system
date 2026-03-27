<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark font-lao"><i class="fas fa-map-marked-alt text-primary me-2"></i>ລາຍການແພັກເກັດທົວ</h2>
            <div class="d-flex gap-2">
                <form action="" method="GET" class="input-group shadow-sm rounded-pill overflow-hidden border">
                    <input type="text" name="search" class="form-control border-0 px-3 shadow-none font-lao" placeholder="ຄົ້ນຫາ..." value="<?php echo $search; ?>">
                    <button class="btn btn-white bg-white border-0" type="submit"><i class="fas fa-search text-muted"></i></button>
                </form>
                <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm font-lao">+ ເພີ່ມທົວ</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ຮູບພາບ</th>
                            <th>ຊື່ແພັກເກັດ</th>
                            <th class="text-center">ບ່ອນນັ່ງ (ຫວ່າງ)</th>
                            <th class="text-end">ລາຄາຂາຍ</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT t.*, v.plate_number, g.fullname as guide_name FROM tours t LEFT JOIN vehicles v ON t.vehicle_id = v.vehicle_id LEFT JOIN guides g ON t.guide_id = g.guide_id";
                        if ($search != '') $sql .= " WHERE t.tour_name LIKE '%$search%'";
                        $sql .= " ORDER BY t.tour_id DESC";
                        $result = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                                $tid = $row['tour_id'];
                                $booked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
                                $remaining = $row['max_seats'] - ($booked['total'] ?? 0);
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <img src="<?php echo BASE_URL; ?>assets/uploads/tours/<?php echo $row['image']; ?>" class="rounded-3 border" width="70" height="45" style="object-fit: cover;">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $row['tour_name']; ?></div>
                                    <small class="text-muted">Code: <?php echo $row['tour_code']; ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?php echo ($remaining <= 2) ? 'bg-danger' : 'bg-info text-dark'; ?> rounded-pill">
                                        <?php echo $remaining; ?> / <?php echo $row['max_seats']; ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-danger"><?php echo number_format($row['price']); ?></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?php echo ($row['status'] == 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill">
                                        <a href="edit.php?id=<?php echo $tid; ?>" class="btn btn-sm btn-white text-warning border-end"><i class="fas fa-edit"></i></a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $tid; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<style> .btn-white { background:#fff; border:none; } .btn-white:hover { background:#f8f9fa; } </style>
<?php include '../../includes/footer.php'; ?>