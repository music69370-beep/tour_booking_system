<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold"><i class="fas fa-bus text-info me-2"></i>ລາຍການພາຫະນະ ແລະ ຄົນຂັບ</h2>
            <a href="add.php" class="btn btn-info text-white rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມລົດໃໝ່
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">ຂໍ້ມູນລົດ</th>
                                <th>ບ່ອນນັ່ງ</th>
                                <th>ຄົນຂັບ / ເບີໂທ</th>
                                <th>ສະຖານະ</th>
                                <th class="text-center">ຈັດການ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($conn, "SELECT * FROM vehicles ORDER BY vehicle_id DESC");
                            while($row = mysqli_fetch_assoc($res)):
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?php echo $row['model']; ?></div>
                                        <span class="badge bg-light text-dark border small"><?php echo $row['plate_number']; ?></span>
                                    </td>
                                    <td class="fw-bold text-primary"><?php echo $row['capacity']; ?> ບ່ອນ</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if(!empty($row['driver_image'])): ?>
                                                <img src="<?php echo BASE_URL; ?>assets/uploads/vehicles/<?php echo $row['driver_image']; ?>" class="rounded-circle me-2 border shadow-sm" width="35" height="35" style="object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center border" style="width: 35px; height: 35px;"><i class="fas fa-user small"></i></div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold small"><?php echo $row['driver_name']; ?></div>
                                                <small class="text-muted small"><?php echo $row['driver_phone']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo ($row['status'] == 'Available') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                            <button class="btn btn-sm btn-white text-info border-end" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $row['vehicle_id']; ?>"><i class="fas fa-eye"></i></button>
                                            <a href="edit.php?id=<?php echo $row['vehicle_id']; ?>" class="btn btn-sm btn-white text-warning border-end"><i class="fas fa-edit"></i></a>
                                            <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['vehicle_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<style> .btn-white { background: #fff; border: none; } .btn-white:hover { background: #f8f9fa; } </style>
<?php include '../../includes/footer.php'; ?>