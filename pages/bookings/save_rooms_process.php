<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['btn_save_rooms']) || isset($_POST['btn_save_master'])) {
    // ຮັບຄ່າ Array
    $bids = is_array($_POST['booking_id']) ? $_POST['booking_id'] : [$_POST['booking_id']];
    $hotels = $_POST['hotel'];
    $names = $_POST['name'];
    $rooms = $_POST['room'];
    
    // ບອກວ່າຈະໃຫ້ Redirect ໄປໃສ
    $return_url = $_POST['return_url'] ?? "view.php?id=" . $bids[0];

    // Loop ບັນທຶກຂໍ້ມູນ
    foreach ($hotels as $index => $hotel_name) {
        $bid = mysqli_real_escape_string($conn, $bids[$index]);
        $h_name = mysqli_real_escape_string($conn, $hotel_name);
        $p_name = mysqli_real_escape_string($conn, $names[$index]);
        $r_num = mysqli_real_escape_string($conn, $rooms[$index]);

        // ລຶບຂອງເກົ່າສະເພາະແຖວນັ້ນ ແລ້ວ Insert ໃໝ່ (ເພື່ອຄວາມແນ່ນອນ)
        mysqli_query($conn, "DELETE FROM booking_room_assignments WHERE booking_id = '$bid' AND hotel_name = '$h_name' AND participant_name = '$p_name'");
        
        if (!empty(trim($r_num))) {
            mysqli_query($conn, "INSERT INTO booking_room_assignments (booking_id, hotel_name, participant_name, room_number) 
                                 VALUES ('$bid', '$h_name', '$p_name', '$r_num')");
        }
    }

    header("Location: " . $return_url . "&msg=updated");
    exit();
}