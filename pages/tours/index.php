<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// ຮັບຄ່າຄົ້ນຫາ (ຖ້າມີ)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-map-marked-alt text-primary me-2"></i>ລາຍການແພັກເກັດທົວທັງໝົດ</h2>
            <div class="d-flex gap-2">
                <form action="" method="GET" class="input-group shadow-sm rounded-pill overflow-hidden border">
                    <input type="text" name="search" class="form-control border-0 px-3 shadow-none" placeholder="ຄົ້ນຫາຊື່ທົວ ຫຼື ລະຫັດ..." value="<?php echo $search; ?>">
                    <button class="btn btn-white bg-white border-0" type="submit"><i class="fas fa-search text-muted"></i></button>
                </form>
                <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">+ ເພີ່ມທົວໃໝ່</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">ຮູບພາບ</th>
                                <th>ຂໍ້ມູນແພັກເກັດ (Code)</th>
                                <th>ພາຫະນະ / ໄກ້ຜູ້ນຳທ່ຽວ</th>
                                <th class="text-center">ບ່ອນນັ່ງ (ຫວ່າງ)</th>
                                <th class="text-end">ລາຄາຂາຍ/ທ່ານ</th>
                                <th class="text-center">ສະຖານະ</th>
                                <th class="text-center">ຈັດການ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // SQL ດຶງຂໍ້ມູນທົວ ພ້ອມ Join ເອົາຂໍ້ມູນລົດ ແລະ ໄກ້ ມາສະແດງ
                            $sql = "SELECT t.*, v.plate_number, v.model as car_model, g.fullname as guide_name 
                                    FROM tours t
                                    LEFT JOIN vehicles v ON t.vehicle_id = v.vehicle_id
                                    LEFT JOIN guides g ON t.guide_id = g.guide_id";
                            
                            if ($search != '') {
                                $sql .= " WHERE t.tour_name LIKE '%$search%' OR t.tour_code LIKE '%$search%'";
                            }
                            
                            $sql .= " ORDER BY t.tour_id DESC";
                            $result = mysqli_query($conn, $sql);

                            if(mysqli_num_rows($result) > 0):
                                while($row = mysqli_fetch_assoc($result)):
                                    // ຄຳນວນບ່ອນນັ່ງຫວ່າງ (ລົບບ່ອນນັ່ງທັງໝົດ ກັບ ຈຳນວນຄົນທີ່ຈອງແລ້ວ)
                                    $tid = $row['tour_id'];
                                    $booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
                                    $booked_count = $booked_res['total'] ?? 0;
                                    $remaining = $row['max_seats'] - $booked_count;
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <img src="<?php echo BASE_URL; ?>assets/uploads/tours/<?php echo $row['image']; ?>" 
                                             class="rounded-3 border shadow-sm" width="70" height="45" style="object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo $row['tour_name']; ?></div>
                                        <span class="badge bg-light text-primary border" style="font-size: 0.7rem;">Code: <?php echo $row['tour_code']; ?></span>
                                    </td>
                                    <td>
                                        <div class="small text-dark">
                                            <i class="fas fa-bus me-1 text-muted"></i> <?php echo $row['car_model'] ?: 'ຍັງບໍ່ໄດ້ກຳນົດ'; ?> (<?php echo $row['plate_number']; ?>)
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-user-tie me-1"></i> <?php echo $row['guide_name'] ?: 'ຍັງບໍ່ມີໄກ້'; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?php echo ($remaining <= 2) ? 'bg-danger' : 'bg-info text-dark'; ?> rounded-pill">
                                            <?php echo $remaining; ?> / <?php echo $row['max_seats']; ?> ບ່ອນ
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-danger"><?php echo number_format($row['price']); ?></div>
                                        <div class="text-muted" style="font-size: 0.7rem;">ທຶນ: <?php echo number_format($row['cost_per_person']); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill <?php echo ($row['status'] == 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ($row['status'] == 'Active') ? 'ເປີດໃຫ້ຈອງ' : 'ປິດການຈອງ'; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                            <a href="edit.php?id=<?php echo $row['tour_id']; ?>" class="btn btn-sm btn-white text-warning border-end" title="ແກ້ໄຂ">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" 
                                               onclick="confirmDelete(<?php echo $row['tour_id']; ?>, 'delete.php')" 
                                               class="btn btn-sm btn-white text-danger" title="ລຶບ">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນແພັກເກັດທົວ</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .btn-white { background-color: #fff; border: none; }
    .btn-white:hover { background-color: #f8f9fa; }
</style>

<?php include '../../includes/footer.php'; ?>