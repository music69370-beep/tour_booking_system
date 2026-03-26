<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold"><i class="fas fa-map-marked-alt text-primary me-2"></i>ລາຍການແພັກເກັດທົວ</h2>
        <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> ເພີ່ມທົວໃໝ່
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">ຮູບພາບ</th>
                            <th>ຊື່ແພັກເກັດທົວ</th>
                            <th>ໄລຍະເວລາ</th>
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
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <img src="../../assets/uploads/tours/<?php echo $row['image']; ?>" 
                                         class="rounded-3" width="80" height="50" style="object-fit: cover;">
                                </td>
                                <td class="fw-bold"><?php echo $row['tour_name']; ?></td>
                                <td><?php echo $row['duration']; ?></td>
                                <td class="text-danger fw-bold"><?php echo number_format($row['price']); ?></td>
                                <td>
                                    <span class="badge rounded-pill <?php echo ($row['status'] == 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                        <a href="edit.php?id=<?php echo $row['tour_id']; ?>" class="btn btn-sm btn-white text-warning border-end">
                                            <i class="fas fa-edit"></i> ແກ້ໄຂ
                                        </a>
                                        
                                        <!-- *** ຈຸດສຳຄັນ: ປ່ຽນປຸ່ມລຶບໃໝ່ *** -->
                                        <a href="javascript:void(0)" 
                                           onclick="confirmDelete(<?php echo $row['tour_id']; ?>, 'delete.php')" 
                                           class="btn btn-sm btn-white text-danger">
                                            <i class="fas fa-trash"></i> ລຶບ
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            endwhile; 
                        else:
                        ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ຍັງບໍ່ມີຂໍ້ມູນແພັກເກັດທົວ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
    .btn-white { background-color: #fff; border: none; }
    .btn-white:hover { background-color: #f8f9fa; }
</style>

<?php include '../../includes/footer.php'; ?>