<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-route text-primary me-2"></i>ປະຫວັດການອອກທົວ ແລະ ສະຖານະ</h2>
            <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="fas fa-plus-circle me-1"></i> + ປ່ອຍລົດໃໝ່
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-uppercase">
                        <tr>
                            <th class="ps-4">ວັນທີເດີນທາງ</th>
                            <th>ຂໍ້ມູນລົດ</th>
                            <th>ແພັກເກັດທົວ</th>
                            <th>ຄົນຂັບ</th>
                            <th class="text-center">ເລກໄມ (ເລີ່ມ-ຈົບ)</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT vo.*, v.plate_number, v.model, t.tour_name, d.fullname as d_name 
                                FROM vehicle_outings vo
                                JOIN vehicles v ON vo.vehicle_id = v.vehicle_id
                                JOIN tours t ON vo.tour_id = t.tour_id
                                JOIN drivers d ON vo.driver_id = d.driver_id
                                ORDER BY vo.outing_id DESC";
                        $res = mysqli_query($conn, $sql);
                        
                        if($res && mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)):
                                $is_on_trip = ($row['status'] == 'On Trip');
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-primary"><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></div>
                                <small class="text-muted">ເຖິງ: <?php echo date('d/m/Y', strtotime($row['return_date'])); ?></small>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo $row['model']; ?></div>
                                <span class="badge bg-light text-dark border small"><?php echo $row['plate_number']; ?></span>
                            </td>
                            <td class="small text-truncate" style="max-width: 150px;"><?php echo $row['tour_name']; ?></td>
                            <td><?php echo $row['d_name']; ?></td>
                            <td class="text-center small">
                                <span class="text-primary fw-bold"><?php echo number_format($row['start_mileage']); ?></span> - 
                                <span class="text-success fw-bold"><?php echo $row['end_mileage'] > 0 ? number_format($row['end_mileage']) : '...'; ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill <?php echo $is_on_trip ? 'bg-primary' : 'bg-success'; ?>">
                                    <?php echo $is_on_trip ? 'ກຳລັງເດີນທາງ' : 'ສຳເລັດແລ້ວ'; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if($is_on_trip): ?>
                                    <button onclick="confirmReturn(<?php echo $row['outing_id']; ?>, <?php echo $row['start_mileage']; ?>)" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">ກັບມາແລ້ວ</button>
                                <?php else: ?>
                                    <span class="text-muted small">Trip Closed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">ຍັງບໍ່ມີລາຍການອອກທົວ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
function confirmReturn(id, startMile) {
    Swal.fire({
        title: 'ບັນທຶກລົດກັບມາ',
        text: 'ປ້ອນເລກໄມຕອນກັບ (ຫຼາຍກວ່າ ' + startMile + ')',
        input: 'number',
        showCancelButton: true,
        confirmButtonText: 'ບັນທຶກ',
        preConfirm: (endMile) => {
            if (!endMile || parseInt(endMile) <= startMile) {
                Swal.showValidationMessage('ເລກໄມບໍ່ຖືກຕ້ອງ!');
            }
            return endMile;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `return_process.php?id=${id}&end_mileage=${result.value}`;
        }
    })
}
</script>
<?php include '../../includes/footer.php'; ?>