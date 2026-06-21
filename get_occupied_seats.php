<?php
// ປິດການໂຊ Error ທີ່ອາດຈະໄປປົນກັບ JSON
error_reporting(0);
include 'config/db.php';
/** @var mysqli $conn */

// ຮັບ ID ຂອງທົວ
$tour_id = isset($_GET['tour_id']) ? mysqli_real_escape_string($conn, $_GET['tour_id']) : 0;

// ດຶງເລກບ່ອນນັ່ງຈາກການຈອງທີ່ Confirm ຫຼື Pending
$sql = "SELECT selected_seats FROM bookings WHERE tour_id = '$tour_id' AND status != 'Cancelled'";
$res = mysqli_query($conn, $sql);

$occupied = [];
if($res) {
    while($row = mysqli_fetch_assoc($res)) {
        if(!empty($row['selected_seats'])) {
            // ແຍກຂໍ້ຄວາມ "1, 2, 5" ອອກເປັນ Array
            $seats = explode(',', $row['selected_seats']);
            foreach($seats as $s) {
                $trimmed_seat = trim($s);
                if($trimmed_seat !== "") {
                    $occupied[] = $trimmed_seat;
                }
            }
        }
    }
}

// ສົ່ງ Header ບອກວ່າເປັນ JSON
header('Content-Type: application/json');
echo json_encode(array_values(array_unique(array_map('strval', $occupied))));
exit();
?>