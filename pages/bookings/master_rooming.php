<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$tour_id = $_GET['tour_id'] ?? '';
$travel_date = $_GET['travel_date'] ?? '';

// ຟັງຊັນດຶງເບີຫ້ອງມາສະແດງໃນ Input
function getRoom($conn, $bid, $hotel, $name) {
    $res = mysqli_query($conn, "SELECT room_number FROM booking_room_assignments WHERE booking_id='$bid' AND hotel_name='$hotel' AND participant_name='$name'");
    $d = mysqli_fetch_assoc($res); return $d['room_number'] ?? '';
}
?>

<style>
    /* ຕົບແຕ່ງພື້ນຫຼັງ ແລະ ຕົວໜັງສື */
    .main-content { background-color: #f0f2f5; }
    .page-title { color: #2d3436; font-weight: 800; letter-spacing: -0.5px; }
    
    /* ຕົບແຕ່ງ Card */
    .custom-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: #fff; }
    
    /* ຕົບແຕ່ງ Table */
    .table-rooming thead th { 
        background-color: #f8f9fc; 
        color: #4e73df; 
        font-weight: 700; 
        text-transform: uppercase; 
        font-size: 0.75rem; 
        letter-spacing: 1px;
        padding: 15px;
        border-bottom: 2px solid #e3e6f0;
    }
    .table-rooming tbody td { padding: 15px; border-bottom: 1px solid #f1f3f7; vertical-align: middle; }
    .table-rooming tbody tr:hover { background-color: #f8f9ff; transition: 0.2s; }
    
    /* Input ແລະ Badge */
    .room-input-box { 
        border-radius: 10px; 
        border: 1px solid #d1d3e2; 
        padding: 8px; 
        background-color: #f8f9fc; 
        transition: 0.3s;
    }
    .room-input-box:focus { background-color: #fff; border-color: #4e73df; box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.1); }
    
    .badge-room { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.7rem; }
    .hotel-label { color: #4e73df; font-weight: 700; font-size: 0.9rem; }
    
    /* ປຸ່ມກົດ */
    .btn-fetch { border-radius: 12px; padding: 10px 25px; transition: 0.3s; }
    .btn-print-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
    .btn-save-floating { 
        position: fixed; 
        bottom: 30px; 
        right: 30px; 
        z-index: 1000; 
        padding: 15px 40px; 
        border-radius: 50px; 
        box-shadow: 0 10px 25px rgba(28,200,138,0.4); 
        font-weight: 700;
    }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title mb-0"><i class="fas fa-bed text-warning me-2"></i>ຈັດການຫ້ອງພັກລວມ</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item small"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item small active">Rooming List</li>
                </ol>
            </nav>
        </div>

        <!-- 1. ສ່ວນກັ່ນຕອງຂໍ້ມູນ -->
        <div class="card custom-card p-4 mb-4">
            <form action="" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="small fw-bold text-muted mb-2"><i class="fas fa-map-marked-alt me-1"></i> 1. ເລືອກແພັກເກັດທົວ:</label>
                    <select name="tour_id" class="form-select form-control border-0 bg-light py-2" required>
                        <option value="">-- ເລືອກແພັກເກັດທົວ --</option>
                        <?php 
                        $t_res = mysqli_query($conn, "SELECT tour_id, tour_name FROM tours");
                        while($t = mysqli_fetch_assoc($t_res)) echo "<option value='{$t['tour_id']}' ".($tour_id==$t['tour_id']?'selected':'').">{$t['tour_name']}</option>";
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-2"><i class="far fa-calendar-alt me-1"></i> 2. ເລືອກວັນທີເດີນທາງ:</label>
                    <input type="date" name="travel_date" class="form-control border-0 bg-light py-2" value="<?php echo $travel_date; ?>" required>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-fetch flex-grow-1 fw-bold shadow-sm">
                            <i class="fas fa-search me-2"></i> ດຶງລາຍຊື່
                        </button>
                        <?php if($tour_id && $travel_date): ?>
                            <a href="print_rooming.php?tour_id=<?php echo $tour_id; ?>&travel_date=<?php echo $travel_date; ?>" 
                               target="_blank" class="btn btn-dark btn-print-icon shadow-sm" title="ພິມລາຍງານ">
                                <i class="fas fa-print"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <?php if($tour_id && $travel_date): ?>
            <!-- 2. ຕາຕະລາງຈັດການຫ້ອງພັກ -->
            <form action="save_rooms_process.php" method="POST" id="masterRoomForm">
                <input type="hidden" name="return_url" value="master_rooming.php?tour_id=<?php echo $tour_id; ?>&travel_date=<?php echo $travel_date; ?>">
                
                <div class="card custom-card overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-rooming mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4" width="120">Booking</th>
                                    <th>ຊື່ຜູ້ໂດຍສານ</th>
                                    <th width="150">ປະເພດຫ້ອງ</th>
                                    <th>ໂຮງແຮມ</th>
                                    <th width="180" class="text-center">ເລກຫ້ອງພັກ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $tour_q = mysqli_query($conn, "SELECT itinerary FROM tours WHERE tour_id='$tour_id'");
                                $tour_data = mysqli_fetch_assoc($tour_q);
                                $iti = json_decode($tour_data['itinerary'], true);
                                $hotels = [];
                                if($iti) foreach($iti as $d) if(isset($d['events'])) foreach($d['events'] as $e) if($e['type']=='hotel') $hotels[] = $e['location'];
                                $hotels = array_unique($hotels);

                                $sql = "SELECT b.booking_id, b.room_type, c.fullname as lead_name 
                                        FROM bookings b 
                                        JOIN customers c ON b.customer_id = c.customer_id 
                                        WHERE b.tour_id='$tour_id' AND b.travel_date='$travel_date' AND b.status='Confirmed'";
                                $res = mysqli_query($conn, $sql);
                                
                                if(mysqli_num_rows($res) > 0):
                                    while($b = mysqli_fetch_assoc($res)):
                                        $bid = $b['booking_id'];
                                        $names = [$b['lead_name']];
                                        $p_res = mysqli_query($conn, "SELECT participant_name FROM booking_participants WHERE booking_id='$bid'");
                                        while($p = mysqli_fetch_assoc($p_res)) $names[] = $p['participant_name'];

                                        foreach($hotels as $h):
                                            foreach($names as $n):
                                ?>
                                    <tr>
                                        <td class="ps-4 small fw-bold text-muted">#BK-<?php echo $bid; ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo $n; ?></div>
                                            <?php if($n == $b['lead_name']) echo '<span class="badge bg-success-subtle text-success small" style="font-size:0.6rem">LEAD</span>'; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-room <?php echo $b['room_type'] == 'Single' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary'; ?>">
                                                <i class="fas <?php echo $b['room_type'] == 'Single' ? 'fa-user' : 'fa-users'; ?> me-1"></i>
                                                <?php 
                                                    // ປ່ຽນການສະແດງຜົນເປັນພາສາລາວ
                                                    if($b['room_type'] == 'Single') {
                                                        echo 'VIP';
                                                    } else {
                                                        echo 'ທຳມະດາ';
                                                    }
                                                ?>
                                            </span>
                                        </td>
                                        <td><div class="hotel-label text-truncate" style="max-width: 200px;"><i class="fas fa-hotel me-1 opacity-50"></i> <?php echo $h; ?></div></td>
                                        <td>
                                            <input type="hidden" name="booking_id[]" value="<?php echo $bid; ?>">
                                            <input type="hidden" name="hotel[]" value="<?php echo $h; ?>">
                                            <input type="hidden" name="name[]" value="<?php echo $n; ?>">
                                            <input type="text" name="room[]" class="form-control form-control-sm text-center room-input-box fw-bold text-primary" 
                                                   value="<?php echo getRoom($conn, $bid, $h, $n); ?>" placeholder="---">
                                        </td>
                                    </tr>
                                <?php endforeach; endforeach; endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <img src="../../assets/img/no-data.png" alt="no data" style="width: 80px; opacity: 0.3; margin-bottom: 15px;">
                                            <p class="text-muted small">ບໍ່ມີລາຍການຈອງທີ່ຢືນຢັນແລ້ວໃນວັນທີນີ້</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if(mysqli_num_rows($res) > 0): ?>
                    <!-- ປຸ່ມ SAVE ລອຍຕົວ (ສີຂຽວ) -->
                    <button type="submit" name="btn_save_master" class="btn btn-success btn-save-floating shadow">
                        <i class="fas fa-save me-2"></i> ບັນທຶກເບີຫ້ອງທັງໝົດ
                    </button>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <!-- ສະແດງຕອນຍັງບໍ່ເລືອກທົວ -->
            <div class="text-center py-5">
                <i class="fas fa-search-location fa-4x text-light mb-3"></i>
                <h5 class="text-muted fw-normal">ກະລຸນາເລືອກແພັກເກັດທົວ ແລະ ວັນທີ ເພື່ອດຶງລາຍຊື່</h5>
            </div>
        <?php endif; ?>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // ເພີ່ມເອັບເຟັກຕອນກົດ Save
    $('#masterRoomForm').on('submit', function() {
        $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin me-2"></i> ກຳລັງບັນທຶກ...').addClass('disabled');
    });
</script>

<?php include '../../includes/footer.php'; ?>