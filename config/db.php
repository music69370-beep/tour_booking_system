<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "tour_booking_db");
mysqli_set_charset($conn, "utf8mb4");

if (!$conn) die("Database Connection Failed");

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/tour_booking_system/');
}

// --- ບ່ອນແກ້ໄຂ: ເພີ່ມລາຍຊື່ໜ້າທີ່ລູກຄ້າເຂົ້າເບິ່ງໄດ້ໂດຍບໍ່ຕ້ອງ Login ---
$current_page = basename($_SERVER['PHP_SELF']);
$allowed_pages = [
    'login.php', 
    'auth_action.php', 
    'setup_db.php', 
    'index.php',           // ໜ້າລາຍການທົວ
    'booking_form.php',     // ໜ້າຟອມຈອງ
    'process_booking.php'   // ໜ້າບັນທຶກການຈອງ
];

// ກວດສອບສິດ: ຖ້າຢູ່ໜ້າທີ່ບໍ່ໄດ້ຮັບອະນຸຍາດ ແລະ ຍັງບໍ່ Login ໃຫ້ເດັ້ງໄປ Login
if (!in_array($current_page, $allowed_pages) && !isset($_SESSION['user_id'])) {
    $path = (strpos($_SERVER['PHP_SELF'], 'pages/') !== false) ? '../../login.php' : 'login.php';
    header("Location: $path");
    exit();
}
?>