<?php
include '../../config/db.php';

// ກວດສອບສິດ (ຕ້ອງ Login ກ່ອນ)
if (!isset($_SESSION['user_id'])) { 
    exit("Permission Denied"); 
}

// ຕັ້ງຄ່າ Header ໃຫ້ Browser ດາວໂຫລດເປັນ CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=tour_bookings_report_' . date('Y-m-d') . '.csv');

// --- ຈຸດທີ່ແກ້ໄຂ: ເພີ່ມ 'w' ເຂົ້າໄປໃນ parameter ທີສອງ ---
$output = fopen('php://output', 'w');

// ເພີ່ມ BOM ເພື່ອໃຫ້ Excel ອ່ານພາສາລາວອອກ (UTF-8)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// ເພີ່ມຫົວຕາຕະລາງ
fputcsv($output, ['ເລກທີການຈອງ', 'ຊື່ລູກຄ້າ', 'ເບີໂທ', 'ຊື່ແພັກເກັດທົວ', 'ຈຳນວນຄົນ', 'ລາຄາລວມ (ກີບ)', 'ວັນທີຈອງ', 'ສະຖານະ']);

// ດຶງຂໍ້ມູນຈາກ Database
$sql = "SELECT b.booking_id, c.fullname, c.phone, t.tour_name, b.num_people, b.total_price, b.booking_date, b.status 
        FROM bookings b
        JOIN customers c ON b.customer_id = c.customer_id
        JOIN tours t ON b.tour_id = t.tour_id
        ORDER BY b.booking_id DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            "#BK-" . str_pad($row['booking_id'], 4, '0', STR_PAD_LEFT),
            $row['fullname'],
            $row['phone'],
            $row['tour_name'],
            $row['num_people'],
            number_format($row['total_price']),
            date('d/m/Y H:i', strtotime($row['booking_date'])),
            $row['status']
        ]);
    }
}

fclose($output);
exit();
?>