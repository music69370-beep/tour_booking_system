<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold"><i class="fas fa-bus text-info me-2"></i>ຈັດການລົດທົວ</h2>
        <a href="add.php" class="btn btn-info text-white rounded-pill px-4 shadow-sm">+ ເພີ່ມລົດໃໝ່</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>ລຸ້ນລົດ</th>
                        <th>ເລກທະບຽນ</th>
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
                            <td><div class="fw-bold"><?php echo $row['model']; ?></div></td>
                            <td><span class="badge bg-light text-dark border"><?php echo $row['plate_number']; ?></span></td>
                            <td><?php echo $row['capacity']; ?> ບ່ອນ</td>
                            <td>
                                <div><?php echo $row['driver_name']; ?></div>
                                <small class="text-muted"><?php echo $row['driver_phone']; ?></small>
                            </td>
                            <td>
                                <span class="badge rounded-pill <?php echo ($row['status']=='Available')?'bg-success':'bg-warning'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm border rounded-pill">
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
</main>
<?php include '../../includes/footer.php'; ?>