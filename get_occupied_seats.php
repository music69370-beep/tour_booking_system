<?php
include 'config/db.php';

// ຮັບ ID ຂອງທົວ
$tour_id = isset($_GET['tour_id']) ? mysqli_real_escape_string($conn, $_GET['tour_id']) : 0;

// ດຶງເລກບ່ອນນັ່ງຈາກການຈອງທີ່ Confirm ຫຼື Pending (ບໍ່ເອົາລາຍການທີ່ Cancelled)
$sql = "SELECT selected_seats FROM bookings WHERE tour_id = '$tour_id' AND status != 'Cancelled'";
$res = mysqli_query($conn, $sql);

$occupied = [];
if($res) {
    while($row = mysqli_fetch_assoc($res)) {
        if(!empty($row['selected_seats'])) {
            // ແຍກຂໍ້ຄວາມ "1,2,5" ອອກເປັນ Array [1, 2, 5]
            $seats = explode(',', $row['selected_seats']);
            $occupied = array_merge($occupied, $seats);
        }
    }
}

// ສົ່ງຄ່າອອກເປັນ JSON Format ແລະ ຈັດການຄ່າທີ່ຊ້ຳກັນ (ຖ້າມີ)
echo json_encode(array_values(array_unique($occupied)));
?>