<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. ປັບ SQL ໃຫ້ດຶງຂໍ້ມູນທົວມາໃຫ້ຄົບ (highlights, included, excluded, etc.)
$sql = "SELECT b.*, c.fullname, c.phone, c.id_card_no, 
               t.tour_name, t.itinerary, t.image as tour_image, t.highlights, 
               t.whats_included, t.whats_excluded, t.duration, t.meeting_point,
               u1.fullname as staff_name, 
               u2.fullname as admin_name
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        LEFT JOIN users u1 ON b.created_by = u1.user_id
        LEFT JOIN users u2 ON b.approved_by = u2.user_id
        WHERE b.booking_id = '$id'";

$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
if (!$row) exit("Not found");

$all_seats = explode(',', $row['selected_seats'] ?? '');

function getRoomValue($conn, $bid, $hotel, $name) {
    $res = mysqli_query($conn, "SELECT room_number FROM booking_room_assignments WHERE booking_id='$bid' AND hotel_name='$hotel' AND participant_name='$name'");
    $d = mysqli_fetch_assoc($res); return $d['room_number'] ?? '';
}
?>

<style>
    .seat-badge-fixed { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-weight: 800; font-size: 1.1rem; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
    .table-custom td { vertical-align: middle; border-bottom: 1px solid #f1f3f7; padding: 12px 10px; }
    .tour-detail-box { border-left: 5px solid #0d6efd; background-color: #f8faff; }
    .strikethrough { text-decoration: line-through !important; color: #adb5bd !important; }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">ລາຍລະອຽດ #BK-<?php echo $id; ?></h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-3 shadow-sm">ຍ້ອນກັບ</a>
        </div>

        <div class="row g-4">
            <!-- ຟາກຊ້າຍ: ຂໍ້ມູນຜູ້ໂດຍສານ ແລະ ການຈັດຫ້ອງ -->
            <div class="col-lg-8">
                
                <!-- 1. ຂໍ້ມູນແພັກເກັດທົວ (ເພີ່ມໃໝ່) -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="../../assets/uploads/tours/<?php echo $row['tour_image']; ?>" class="img-fluid h-100 w-100" style="object-fit: cover;">
                        </div>
                        <div class="col-md-8 p-4">
                            <div class="badge bg-primary-subtle text-primary rounded-pill px-3 mb-2"><?php echo $row['duration']; ?></div>
                            <h4 class="fw-bold text-dark"><?php echo $row['tour_name']; ?></h4>
                            <p class="small text-muted mb-3"><i class="fas fa-map-marker-alt me-2"></i>ຈຸດນັດພົບ: <?php echo $row['meeting_point']; ?></p>
                            <div class="p-3 rounded-4 tour-detail-box">
                                <h6 class="fw-bold small mb-2 text-primary">ຈຸດເດັ່ນຂອງທົວ (Highlights):</h6>
                                <div class="small text-dark"><?php echo nl2br($row['highlights']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ລາຍຊື່ຜູ້ໂດຍສານ -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white py-3 border-0"><h5 class="fw-bold mb-0">ລາຍຊື່ຜູ້ເດີນທາງ</h5></div>
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead><tr><th class="ps-4">#</th><th>ຊື່ ແລະ ນາມສະກຸນ</th><th>ບັດປະຈຳໂຕ</th><th class="text-center">ບ່ອນນັ່ງ</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4">01</td>
                                    <td><div class="fw-bold text-primary"><?php echo $row['fullname']; ?></div><small class="badge bg-success-subtle text-success small px-2">ຫົວໜ້າຄະນະ</small></td>
                                    <td><?php echo $row['id_card_no'] ?: '---'; ?></td>
                                    <td class="text-center"><div class="badge bg-primary seat-badge-fixed mx-auto"><?php echo trim($all_seats[0] ?? '-'); ?></div></td>
                                </tr>
                                <?php 
                                $p_res = mysqli_query($conn, "SELECT * FROM booking_participants WHERE booking_id = '$id'");
                                $i = 2; while($p = mysqli_fetch_assoc($p_res)): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></td>
                                    <td><div class="fw-bold"><?php echo $p['participant_name']; ?></div></td>
                                    <td><?php echo $p['participant_id_card'] ?: '---'; ?></td>
                                    <td class="text-center"><div class="badge bg-info seat-badge-fixed mx-auto text-white"><?php echo isset($all_seats[$i-1]) ? trim($all_seats[$i-1]) : '-'; ?></div></td>
                                </tr>
                                <?php $i++; endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Rooming List -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-0"><h5 class="fw-bold text-dark">ຈັດການເບີຫ້ອງພັກ</h5></div>
                    <div class="card-body p-4 pt-0">
                        <form action="save_rooms_process.php" method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo $id; ?>">
                        <?php 
                        $iti = json_decode($row['itinerary'], true); $hotels = [];
                        if($iti) foreach($iti as $d) if(isset($d['events'])) foreach($d['events'] as $e) if($e['type']=='hotel') $hotels[] = $e['location'];
                        $hotels = array_unique($hotels);
                        if(count($hotels) > 0):
                            foreach($hotels as $h): ?>
                            <div class="mb-4 p-3 bg-light rounded-4 border">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-hotel"></i> <?php echo $h; ?></h6>
                                <table class="table table-sm bg-white rounded-3">
                                    <tbody>
                                        <tr><td><?php echo $row['fullname']; ?></td><td><input type="hidden" name="hotel[]" value="<?php echo $h; ?>"><input type="hidden" name="name[]" value="<?php echo $row['fullname']; ?>"><input type="text" name="room[]" class="form-control form-control-sm text-center" value="<?php echo getRoomValue($conn, $id, $h, $row['fullname']); ?>" placeholder="ເບີຫ້ອງ"></td></tr>
                                        <?php mysqli_data_seek($p_res, 0); while($p = mysqli_fetch_assoc($p_res)): ?>
                                        <tr><td><?php echo $p['participant_name']; ?></td><td><input type="hidden" name="hotel[]" value="<?php echo $h; ?>"><input type="hidden" name="name[]" value="<?php echo $p['participant_name']; ?>"><input type="text" name="room[]" class="form-control form-control-sm text-center" value="<?php echo getRoomValue($conn, $id, $h, $p['participant_name']); ?>" placeholder="ເບີຫ້ອງ"></td></tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center"><button type="submit" name="btn_save_rooms" class="btn btn-success rounded-pill px-5 shadow fw-bold">ບັນທຶກເບີຫ້ອງ</button></div>
                        <?php else: echo "<p class='text-center text-muted py-4'>ບໍ່ມີຂໍ້ມູນໂຮງແຮມໃນແຜນການເດີນທາງ</p>"; endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ຟາກຂວາ: ການເງິນ, Tracking ແລະ ສິ່ງທີ່ລວມ -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark text-white text-center mb-4 shadow-lg">
                    <h6 class="opacity-75 small fw-bold">ຍອດເງິນລວມທັງໝົດ</h6>
                    <h2 class="fw-bold text-success display-6">₭ <?php echo number_format($row['total_price']); ?></h2>
                    <span class="badge bg-primary px-3 py-2 rounded-pill mt-2">Room: <?php echo $row['room_type']; ?></span>
                </div>

                <!-- ປະຫວັດການດຳເນີນການ -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark"><i class="fas fa-history me-2 text-primary"></i>ປະຫວັດການດຳເນີນການ</h6>
                    <div class="mb-3">
                        <small class="text-muted d-block small">📝 ຜູ້ບັນທຶກການຈອງ:</small>
                        <span class="fw-bold text-dark"><?php echo $row['staff_name'] ?: 'ລູກຄ້າຈອງເອງ (Online)'; ?></span>
                    </div>
                    <div>
                        <small class="text-muted d-block small">✅ ຜູ້ອະນຸມັດລາຍການ:</small>
                        <span class="fw-bold <?php echo $row['admin_name'] ? 'text-success' : 'text-warning'; ?>">
                            <?php echo $row['admin_name'] ?: 'ລໍຖ້າການອະນຸມັດ...'; ?>
                        </span>
                    </div>
                </div>

                <!-- ລາຍລະອຽດການບໍລິການ (Included / Excluded) -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark"><i class="fas fa-concierge-bell me-2 text-success"></i>ລາຍລະອຽດການບໍລິການ</h6>
                    <div class="mb-3">
                        <small class="fw-bold text-success"><i class="fas fa-check-circle me-1"></i> ສິ່ງທີ່ລວມ:</small>
                        <div class="small text-muted ps-3 mt-1"><?php echo nl2br($row['whats_included']); ?></div>
                    </div>
                    <div>
                        <small class="fw-bold text-danger"><i class="fas fa-times-circle me-1"></i> ບໍ່ລວມ:</small>
                        <div class="small text-muted ps-3 mt-1"><?php echo nl2br($row['whats_excluded']); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>