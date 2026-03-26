<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h2 class="fw-bold"><i class="fas fa-calendar-check text-primary me-2"></i>ລາຍການຈອງທົວ</h2>
        <div class="d-flex gap-2">
            <!-- ປຸ່ມ Export Excel -->
            <a href="export.php" class="btn btn-success rounded-pill px-3 shadow-sm">
                <i class="fas fa-file-excel me-1"></i> ສົ່ງອອກ Excel
            </a>
            <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> ສ້າງການຈອງໃໝ່
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">ລູກຄ້າ / ເບີໂທ</th>
                        <th>ທົວ</th>
                        <th class="text-center">ຈຳນວນ</th>
                        <th class="text-end">ລາຄາລວມ</th>
                        <th class="text-center">ສະຖານະ</th>
                        <th class="text-center">ຈັດການ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT b.*, c.fullname, c.phone, t.tour_name 
                            FROM bookings b
                            JOIN customers c ON b.customer_id = c.customer_id
                            JOIN tours t ON b.tour_id = t.tour_id
                            ORDER BY b.booking_id DESC";
                    $result = mysqli_query($conn, $sql);

                    while($row = mysqli_fetch_assoc($result)):
                        // ກຽມຂໍ້ຄວາມສຳລັບ WhatsApp
                        $wa_msg = "ສະບາຍດີ " . $row['fullname'] . ", ຂ້ອຍຕິດຕໍ່ຈາກ TourBooking ກ່ຽວກັບການຈອງ " . $row['tour_name'] . " ຂອງເຈົ້າ (ເລກທີ #BK-" . $row['booking_id'] . ").";
                        $wa_url = "https://wa.me/856" . str_replace([' ', '-', '020'], '', $row['phone']) . "?text=" . urlencode($wa_msg);
                    ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?php echo $row['fullname']; ?></div>
                                <small class="text-muted"><i class="fas fa-phone-alt me-1"></i> <?php echo $row['phone']; ?></small>
                            </td>
                            <td><?php echo $row['tour_name']; ?></td>
                            <td class="text-center"><?php echo $row['num_people']; ?> ຄົນ</td>
                            <td class="text-end fw-bold text-danger"><?php echo number_format($row['total_price']); ?> ກີບ</td>
                            <td class="text-center">
                                <span class="badge rounded-pill <?php echo ($row['status']=='Confirmed') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                    <!-- ປຸ່ມ WhatsApp -->
                                    <a href="<?php echo $wa_url; ?>" target="_blank" class="btn btn-sm btn-white text-success border-end" title="ຕິດຕໍ່ WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a href="view.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-white text-primary border-end">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['booking_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<style> .btn-white { background: #fff; border: none; } .btn-white:hover { background: #f8f9fa; } </style>

<?php include '../../includes/footer.php'; ?>