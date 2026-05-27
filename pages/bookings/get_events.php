<?php
include '../../config/db.php';
/** @var mysqli $conn */
$sql = "SELECT b.booking_id, b.travel_date, b.status, c.fullname, t.tour_name 
        FROM bookings b
        JOIN customers c ON b.customer_id = c.customer_id
        JOIN tours t ON b.tour_id = t.tour_id
        WHERE b.status != 'Cancelled'";

$result = mysqli_query($conn, $sql);
$events = [];

while($row = mysqli_fetch_assoc($result)) {
    // ກຳນົດສີຕາມສະຖານະ
    $color = ($row['status'] == 'Confirmed') ? '#198754' : '#ffc107';
    $textColor = ($row['status'] == 'Confirmed') ? '#ffffff' : '#000000';

    $events[] = [
        'id' => $row['booking_id'],
        'title' => $row['tour_name'] . " - " . $row['fullname'],
        'start' => $row['travel_date'],
        'url' => 'view.php?id=' . $row['booking_id'],
        'backgroundColor' => $color,
        'borderColor' => $color,
        'textColor' => $textColor
    ];
}

echo json_encode($events);
?>