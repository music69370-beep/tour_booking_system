<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */

if (!isset($_GET['tour_id']) || !isset($_GET['travel_date'])) { exit("Missing Data"); }

$tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);
$travel_date = mysqli_real_escape_string($conn, $_GET['travel_date']);

// 1. ດຶງຂໍ້ມູນທົວ
$t_res = mysqli_query($conn, "SELECT tour_name, tour_code FROM tours WHERE tour_id = '$tour_id'");
$tour = mysqli_fetch_assoc($t_res);
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>Rooming List - <?php echo $tour['tour_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background: #fff; padding: 20px; color: #000; }
        .print-container { max-width: 900px; margin: 0 auto; border: 1px solid #eee; padding: 40px; }
        .header-title { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .table-report th { background-color: #f2f2f2 !important; color: #000; border: 1px solid #000 !important; }
        .table-report td { border: 1px solid #000 !important; padding: 8px 12px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .print-container { border: none; width: 100%; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="container no-print text-center mb-4">
    <button onclick="window.print()" class="btn btn-dark btn-lg rounded-pill px-5 shadow">
        <i class="fas fa-print me-2"></i> ກົດເພື່ອປິ້ນເອກະສານ (Print)
    </button>
    <button onclick="window.close()" class="btn btn-light border rounded-pill px-4 ms-2">ປິດໜ້ານີ້</button>
</div>

<div class="print-container">
    <!-- Header ເອກະສານ -->
    <div class="row header-title align-items-center">
        <div class="col-6">
            <h3 class="fw-bold mb-0">ໃບຈັດສັນຫ້ອງພັກ</h3>
            <p class="mb-0">OFFICIAL ROOMING LIST</p>
        </div>
        <div class="col-6 text-end">
            <h5 class="fw-bold mb-0">TourBooking System</h5>
            <small>ວັນທີອອກເອກະສານ: <?php echo date('d/m/Y H:i'); ?></small>
        </div>
    </div>

    <!-- ຂໍ້ມູນທົວ -->
    <div class="row my-4">
        <div class="col-7">
            <div class="mb-1">ແພັກເກັດທົວ: <strong><?php echo $tour['tour_name']; ?></strong></div>
            <div>ລະຫັດ: <strong><?php echo $tour['tour_code']; ?></strong></div>
        </div>
        <div class="col-5 text-end">
            <div class="mb-1">ວັນທີເດີນທາງ: <strong class="text-danger"><?php echo date('d/m/Y', strtotime($travel_date)); ?></strong></div>
            <div>ສະຖານະ: <strong>Confirmed</strong></div>
        </div>
    </div>

    <?php 
    // ຊອກຫາລາຍຊື່ໂຮງແຮມທີ່ມີການຈັດຫ້ອງ
    $h_sql = "SELECT DISTINCT hotel_name FROM booking_room_assignments WHERE booking_id IN (SELECT booking_id FROM bookings WHERE tour_id='$tour_id' AND travel_date='$travel_date')";
    $h_res = mysqli_query($conn, $h_sql);
    
    if(mysqli_num_rows($h_res) > 0):
        while($h_row = mysqli_fetch_assoc($h_res)):
            $hotel = $h_row['hotel_name'];
    ?>
        <div class="mt-5">
            <h5 class="fw-bold"><i class="fas fa-hotel"></i> ໂຮງແຮມ: <?php echo $hotel; ?></h5>
            <table class="table table-report w-100">
                <thead>
                    <tr class="text-center">
                        <th width="100">ເລກຫ້ອງ</th>
                        <th>ຊື່ ແລະ ນາມສະກຸນ ຜູ້ພັກ</th>
                        <th width="150">ປະເພດຫ້ອງ</th>
                        <th width="150">ໝາຍເຫດ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // ດຶງຂໍ້ມູນການຈັດຫ້ອງ ໂດຍຈັດລຽງຕາມ "ເລກຫ້ອງ" ເພື່ອໃຫ້ຄົນນອນນຳກັນຢູ່ລຽງກັນ
                    $sql = "SELECT ra.*, b.room_type 
                            FROM booking_room_assignments ra
                            JOIN bookings b ON ra.booking_id = b.booking_id
                            WHERE b.tour_id = '$tour_id' 
                            AND b.travel_date = '$travel_date' 
                            AND ra.hotel_name = '$hotel'
                            ORDER BY ra.room_number ASC, ra.participant_name ASC";
                    $res = mysqli_query($conn, $sql);
                    while($row = mysqli_fetch_assoc($res)):
                    ?>
                    <tr>
                        <td class="text-center fw-bold fs-5"><?php echo $row['room_number']; ?></td>
                        <td><?php echo $row['participant_name']; ?></td>
                        <td class="text-center"><?php echo $row['room_type']; ?></td>
                        <td></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endwhile; else: ?>
        <div class="text-center py-5 border">
            <p class="text-muted">ຍັງບໍ່ມີຂໍ້ມູນການຈັດສັນຫ້ອງພັກ</p>
        </div>
    <?php endif; ?>

    <!-- ສ່ວນລາຍເຊັນ -->
    <div class="row mt-5 pt-5">
        <div class="col-4 text-center">
            <hr style="width: 80%; margin: 0 auto 10px;">
            <p class="small">ຜູ້ຈັດສັນ (Tour Op)</p>
        </div>
        <div class="col-4"></div>
        <div class="col-4 text-center">
            <hr style="width: 80%; margin: 0 auto 10px;">
            <p class="small">ເຈົ້າໜ້າທີ່ໂຮງແຮມ (Hotel Staff)</p>
        </div>
    </div>
</div>

</body>
</html>