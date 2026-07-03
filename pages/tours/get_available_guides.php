<?php
// ປິດການໂຊ Error ທີ່ເປັນ HTML ເພື່ອໃຫ້ JSON ສະອາດ
error_reporting(0);
include '../../config/db.php';
/** @var mysqli $conn */

// ຮັບຄ່າວັນທີ
$start = isset($_GET['start']) ? $_GET['start'] : '';
$end = isset($_GET['end']) ? $_GET['end'] : '';

$guides = [];

if ($start != '' && $end != '') {
    // SQL: ຊອກຫາໄກ້ທີ່ "ວ່າງ" (ບໍ່ຕິດທົວອື່ນໃນຊ່ວງວັນທີນີ້)
    $sql = "SELECT guide_id, fullname FROM guides 
            WHERE guide_id NOT IN (
                SELECT tag.guide_id 
                FROM tour_assigned_guides tag
                JOIN tours t ON tag.tour_id = t.tour_id
                WHERE t.status = 'Active' 
                AND (
                    ('$start' <= t.end_date AND '$end' >= t.start_date)
                )
            ) ORDER BY fullname ASC";

    $res = mysqli_query($conn, $sql);
    if($res){
        while ($row = mysqli_fetch_assoc($res)) {
            $guides[] = $row;
        }
    }
}

// ສົ່ງຄ່າກັບເປັນ JSON
header('Content-Type: application/json');
echo json_encode($guides);
exit();