<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

$sql = "SELECT b.*, c.fullname, c.phone, c.id_card_no, t.tour_name, t.itinerary 
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        WHERE b.booking_id = '$id'";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
if (!$row) exit("Not found");

$all_seats = explode(',', $row['selected_seats']);

function getRoomValue($conn, $bid, $hotel, $name) {
    $res = mysqli_query($conn, "SELECT room_number FROM booking_room_assignments WHERE booking_id='$bid' AND hotel_name='$hotel' AND participant_name='$name'");
    $d = mysqli_fetch_assoc($res); return $d['room_number'] ?? '';
}
?>

<style>
    .seat-badge-fixed { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-weight: 800; font-size: 1.1rem; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
    .table-custom thead { background-color: #f8f9fa; }
    .table-custom td { vertical-align: middle; border-bottom: 1px solid #f1f3f7; padding: 12px 10px; }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">ລາຍລະອຽດ #BK-<?php echo $id; ?></h2>
            <span class="badge bg-primary px-3 py-2 rounded-pill">Room: <?php echo $row['room_type']; ?></span>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- 1. ລາຍຊື່ຜູ້ໂດຍສານທັງໝົດ -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white py-3 border-0"><h5 class="fw-bold mb-0">ລາຍຊື່ຜູ້ເດີນທາງ</h5></div>
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead><tr><th class="ps-4">#</th><th>ຊື່ ແລະ ນາມສະກຸນ</th><th>ບັດປະຈຳໂຕ</th><th class="text-center">ບ່ອນນັ່ງ</th></tr></thead>
                            <tbody>
                                <!-- Lead Customer -->
                                <tr>
                                    <td class="ps-4">01</td>
                                    <td><div class="fw-bold"><?php echo $row['fullname']; ?></div><small class="badge bg-success-subtle text-success small px-2">Lead</small></td>
                                    <td><?php echo $row['id_card_no'] ?: '---'; ?></td>
                                    <td class="text-center"><div class="badge bg-primary seat-badge-fixed mx-auto"><?php echo $all_seats[0] ?? '-'; ?></div></td>
                                </tr>
                                <!-- Participants -->
                                <?php 
                                $p_res = mysqli_query($conn, "SELECT * FROM booking_participants WHERE booking_id = '$id'");
                                $i = 2; while($p = mysqli_fetch_assoc($p_res)): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></td>
                                    <td><div class="fw-bold"><?php echo $p['participant_name']; ?></div></td>
                                    <td><?php echo $p['participant_id_card'] ?: '---'; ?></td>
                                    <td class="text-center"><div class="badge bg-info seat-badge-fixed mx-auto text-white"><?php echo $all_seats[$i-1] ?? '-'; ?></div></td>
                                </tr>
                                <?php $i++; endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Rooming List Form -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-0"><h5 class="fw-bold text-dark">ຈັດການເບີຫ້ອງພັກ</h5></div>
                    <div class="card-body p-4 pt-0">
                        <form action="save_rooms_process.php" method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo $id; ?>">
                        <?php 
                        $iti = json_decode($row['itinerary'], true);
                        $hotels = [];
                        if($iti) foreach($iti as $d) if(isset($d['events'])) foreach($d['events'] as $e) if($e['type']=='hotel') $hotels[] = $e['location'];
                        $hotels = array_unique($hotels);
                        if(count($hotels) > 0):
                            foreach($hotels as $h): ?>
                            <div class="mb-4 p-3 bg-light rounded-4 border">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-hotel"></i> <?php echo $h; ?></h6>
                                <table class="table table-sm bg-white rounded-3">
                                    <tbody>
                                        <tr>
                                            <td><?php echo $row['fullname']; ?></td>
                                            <td>
                                                <input type="hidden" name="hotel[]" value="<?php echo $h; ?>"><input type="hidden" name="name[]" value="<?php echo $row['fullname']; ?>">
                                                <input type="text" name="room[]" class="form-control form-control-sm text-center" value="<?php echo getRoomValue($conn, $id, $h, $row['fullname']); ?>" placeholder="ເບີຫ້ອງ">
                                            </td>
                                        </tr>
                                        <?php mysqli_data_seek($p_res, 0); while($p = mysqli_fetch_assoc($p_res)): ?>
                                        <tr>
                                            <td><?php echo $p['participant_name']; ?></td>
                                            <td>
                                                <input type="hidden" name="hotel[]" value="<?php echo $h; ?>"><input type="hidden" name="name[]" value="<?php echo $p['participant_name']; ?>">
                                                <input type="text" name="room[]" class="form-control form-control-sm text-center" value="<?php echo getRoomValue($conn, $id, $h, $p['participant_name']); ?>" placeholder="ເບີຫ້ອງ">
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center"><button type="submit" name="btn_save_rooms" class="btn btn-success rounded-pill px-5 shadow fw-bold">ບັນທຶກເບີຫ້ອງທັງໝົດ</button></div>
                        <?php else: echo "<p class='text-center text-muted py-4'>ບໍ່ມີຂໍ້ມູນໂຮງແຮມ</p>"; endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark text-white text-center">
                    <h6 class="opacity-75 small">ຍອດເງິນລວມ</h6>
                    <h2 class="fw-bold text-success display-6">₭ <?php echo number_format($row['total_price']); ?></h2>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>