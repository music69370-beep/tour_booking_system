<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// ຈຸດທີ່ແກ້ໄຂ: ໃຊ້ GROUP_CONCAT ເພື່ອດຶງຊື່ໄກ້ທຸກຄົນມາໂຊ
$sql = "SELECT t.*, 
       (SELECT SUM(num_people) FROM bookings WHERE tour_id = t.tour_id AND status != 'Cancelled') as booked_count,
       GROUP_CONCAT(g.fullname SEPARATOR ', ') as all_guides
       FROM tours t
       LEFT JOIN tour_assigned_guides tag ON t.tour_id = tag.tour_id
       LEFT JOIN guides g ON tag.guide_id = g.guide_id";

if ($search != '') {
    $sql .= " WHERE t.tour_name LIKE '%$search%' OR t.tour_code LIKE '%$search%'";
}

$sql .= " GROUP BY t.tour_id ORDER BY t.tour_id DESC";
$result = mysqli_query($conn, $sql);
?>

<style>
    .main-content { background-color: #f8f9fa; }
    .tour-table img { transition: 0.3s; object-fit: cover; border-radius: 12px; }
    .tour-table tr:hover { background-color: #f1f4f9; }
    .badge-subtle { padding: 6px 12px; border-radius: 50px; font-weight: 600; font-size: 0.75rem; }
    .bg-success-subtle { background-color: #e1f5ea; color: #198754; }
    .bg-primary-subtle { background-color: #e7f1ff; color: #0d6efd; }
    .progress { background-color: #e9ecef; border-radius: 50px; overflow: hidden; }
    .btn-action { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 10px; transition: 0.3s; }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1"><i class="fas fa-map-marked-alt text-primary me-2"></i>ລາຍງານແພັກເກັດທົວ</h2>
                <p class="text-muted small">ຈັດການ ແລະ ຕິດຕາມສະຖານະແພັກເກັດທົວທັງໝົດ</p>
            </div>
            <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມທົວໃໝ່
            </a>
        </div>

        <!-- Search Card -->
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group border rounded-pill px-3 py-1">
                        <span class="input-group-text bg-transparent border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 shadow-none bg-transparent" placeholder="ຄົ້ນຫາຊື່ທົວ ຫຼື ລະຫັດແພັກເກັດ..." value="<?php echo $search; ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold">ຄົ້ນຫາ</button>
                </div>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 tour-table">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ຮູບພາບ & ລະຫັດ</th>
                            <th>ຂໍ້ມູນແພັກເກັດ</th>
                            <th>ໄກ້ຜູ້ນຳທ່ຽວ (ທັງໝົດ)</th>
                            <th class="text-center" width="180">ບ່ອນນັ່ງ (Booked)</th>
                            <th class="text-end">ລາຄາ/ທ່ານ</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $booked = $row['booked_count'] ?? 0;
                            $max = $row['max_seats'];
                            $percent = ($max > 0) ? ($booked / $max) * 100 : 0;
                            $remaining = $max - $booked;
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <?php 
                                        $img = $row['image'];
                                        // ກວດເຊັກວ່າເປັນ Link ອອນລາຍ ຫຼື ຮູບໃນເຄື່ອງ
                                        $display_img = (strpos($img, 'http') === 0) ? $img : "../../assets/uploads/tours/" . $img;
                                    ?>
                                    <img src="<?php echo $display_img; ?>" width="90" height="60" class="shadow-sm border" style="object-fit: cover; border-radius: 8px;">
                                    
                                    <div class="ms-3">
                                        <div class="badge bg-primary-subtle badge-subtle mb-1"><?php echo $row['tour_code']; ?></div>
                                        <div class="text-muted small"><?php echo $row['category']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6"><?php echo $row['tour_name']; ?></div>
                                <div class="small text-muted mt-1">
                                    <i class="far fa-calendar-alt text-primary me-1"></i> 
                                    <?php echo date('d/m/Y', strtotime($row['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($row['end_date'])); ?>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info me-2"><i class="fas fa-user-tie"></i></div>
                                    <div class="small fw-bold text-dark">
                                        <?php echo $row['all_guides'] ?: '<span class="text-muted fw-normal">ຍັງບໍ່ມີໄກ້</span>'; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-between mb-1 small fw-bold">
                                    <span class="text-primary"><?php echo $booked; ?> ຈອງ</span>
                                    <span class="text-muted">ເຕັມ <?php echo $max; ?></span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar <?php echo ($percent > 80) ? 'bg-danger' : 'bg-primary'; ?>" style="width: <?php echo $percent; ?>%"></div>
                                </div>
                                <small class="text-muted mt-1 d-block" style="font-size: 0.65rem;">ວ່າງ: <?php echo $remaining; ?> ບ່ອນນັ່ງ</small>
                            </td>
                            <td class="text-end">
                                <div class="fw-bold text-danger fs-5"><?php echo number_format($row['price']); ?></div>
                                <small class="text-muted">ກີບ/LAK</small>
                            </td>
                            <td class="text-center">
                                <?php 
                                $today = date('Y-m-d');
                                if ($row['start_date'] <= $today) {
                                    echo '<span class="badge rounded-pill bg-secondary text-white px-3 py-2">ຈົບ/ເດີນທາງແລ້ວ</span>';
                                } else {
                                    if ($row['status'] == 'Active') {
                                        echo '<span class="badge badge-subtle bg-success-subtle px-3 py-2">ເປີດຂາຍ</span>';
                                    } else {
                                        echo '<span class="badge badge-subtle bg-danger-subtle px-3 py-2">ປິດການຈອງ</span>';
                                    }
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="edit.php?id=<?php echo $row['tour_id']; ?>" class="btn btn-light btn-action text-warning border shadow-sm"><i class="fas fa-edit"></i></a>
                                    <button onclick="confirmDelete(<?php echo $row['tour_id']; ?>, 'delete.php')" class="btn btn-light btn-action text-danger border shadow-sm"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>