<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// ຮັບຄ່າການຄົ້ນຫາ
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-map-marked-alt text-primary me-2"></i>ລາຍງານແພັກເກັດທົວທັງໝົດ</h2>
            <div class="d-flex gap-2">
                <form action="" method="GET" class="input-group shadow-sm rounded-pill overflow-hidden border">
                    <input type="text" name="search" class="form-control border-0 px-3 shadow-none" placeholder="ຄົ້ນຫາຊື່ ຫຼື ລະຫັດ..." value="<?php echo $search; ?>">
                    <button class="btn btn-white bg-white border-0" type="submit"><i class="fas fa-search text-muted"></i></button>
                </form>
                <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow">+ ເພີ່ມທົວໃໝ່</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ຮູບພາບ</th>
                            <th>ຂໍ້ມູນແພັກເກັດ</th>
                            <th>ວັນທີເດີນທາງ</th>
                            <th class="text-center">ບ່ອນນັ່ງ</th>
                            <th class="text-end">ລາຄາ/ທ່ານ</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // --- SQL ທີ່ປັບປຸງໃໝ່: ຕັດການ JOIN ລົດ ແລະ ໄກ້ອອກ ເພື່ອແກ້ Error ---
                        $sql = "SELECT * FROM tours";
                        if ($search != '') { 
                            $sql .= " WHERE tour_name LIKE '%$search%' OR tour_code LIKE '%$search%'"; 
                        }
                        $sql .= " ORDER BY tour_id DESC";
                        
                        $result = mysqli_query($conn, $sql);

                        if($result && mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                                $tid = $row['tour_id'];
                                $st = $row['status'];
                        ?>
                        <tr>
                            <td class="ps-4">
                                <img src="../../assets/uploads/tours/<?php echo $row['image']; ?>" class="rounded-3 border shadow-sm" width="80" height="50" style="object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo $row['tour_name']; ?></div>
                                <div class="d-flex gap-2 mt-1">
                                    <span class="badge bg-light text-primary border small fw-normal"><?php echo $row['tour_code']; ?></span>
                                    <span class="badge bg-primary-subtle text-primary small fw-normal"><?php echo $row['category']; ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-bold text-success"><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></div>
                                <small class="text-muted">ເຖິງ: <?php echo date('d/m/Y', strtotime($row['end_date'])); ?></small>
                            </td>
                            <td class="text-center fw-bold text-dark"><?php echo $row['max_seats']; ?></td>
                            <td class="text-end fw-bold text-danger"><?php echo number_format($row['price']); ?></td>
                            <td class="text-center">
                                <span class="badge rounded-pill <?php echo ($st == 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo ($st == 'Active') ? 'ເປີດ' : 'ປິດ'; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group border rounded-pill overflow-hidden shadow-sm">
                                    <a href="edit.php?id=<?php echo $tid; ?>" class="btn btn-sm btn-white text-warning border-end" title="ແກ້ໄຂ"><i class="fas fa-edit"></i></a>
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $tid; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger" title="ລຶບ"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">ບໍ່ມີຂໍ້ມູນແພັກເກັດທົວ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
    .btn-white { background: #fff; border: none; } 
    .btn-white:hover { background: #f8f9fa; }
    .bg-primary-subtle { background-color: #e7f1ff; }
</style>

<?php include '../../includes/footer.php'; ?>