<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2 class="fw-bold text-dark"><i class="fas fa-clipboard-list text-info me-2"></i>ຕາຕະລາງມອບໝາຍວຽກໄກ້ (ທັງໝົດ)</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ກັບໄປໜ້າລາຍຊື່</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ຊື່ໄກ້ຜູ້ນຳທ່ຽວ</th>
                            <th>ແພັກເກັດທົວທີ່ມອບໝາຍ</th>
                            <th class="text-center">ວັນທີເລີ່ມ</th>
                            <th class="text-center">ວັນທີສິ້ນສຸດ</th>
                            <th class="text-center">ສະຖານະທົວ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // SQL: ດຶງຂໍ້ມູນການມອບໝາຍວຽກທັງໝົດ ຂອງໄກ້ທຸກຄົນ
                        $sql = "SELECT g.fullname, t.tour_name, t.start_date, t.end_date, t.status, t.tour_id
                                FROM tour_assigned_guides tag
                                JOIN guides g ON tag.guide_id = g.guide_id
                                JOIN tours t ON tag.tour_id = t.tour_id
                                ORDER BY t.start_date ASC";
                        $res = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)):
                                $today = date('Y-m-d');
                                $is_active = ($row['status'] == 'Active' && $row['end_date'] >= $today);
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><i class="fas fa-user-circle me-1 text-primary"></i> <?php echo $row['fullname']; ?></div>
                            </td>
                            <td>
                                <div class="text-dark fw-bold"><?php echo $row['tour_name']; ?></div>
                            </td>
                            <td class="text-center text-primary fw-bold">
                                <?php echo date('d/m/Y', strtotime($row['start_date'])); ?>
                            </td>
                            <td class="text-center text-danger fw-bold">
                                <?php echo date('d/m/Y', strtotime($row['end_date'])); ?>
                            </td>
                            <td class="text-center">
                                <?php if($is_active): ?>
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success px-3">ກຳລັງດຳເນີນການ</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-light text-muted border px-3">ສຳເລັດ/ປິດແລ້ວ</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="../tours/edit.php?id=<?php echo $row['tour_id']; ?>" class="btn btn-sm btn-light border text-warning">
                                    <i class="fas fa-edit"></i> ແກ້ໄຂທົວ
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3 opacity-25"></i>
                                    <p>ຍັງບໍ່ມີການມອບໝາຍວຽກໃຫ້ໄກ້ໃນລະບົບ</p>
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
    .bg-success-subtle { background-color: #eefaf4; }
    .main-content { background-color: #f8f9fa; }
</style>

<?php include '../../includes/footer.php'; ?>