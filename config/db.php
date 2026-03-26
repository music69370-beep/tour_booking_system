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

$current_page = basename($_SERVER['PHP_SELF']);
// --- ເພີ່ມ register.php ແລະ register_action.php ບ່ອນນີ້ ---
$allowed_pages = [
    'login.php', 
    'auth_action.php', 
    'setup_db.php', 
    'index.php', 
    'booking_form.php', 
    'process_booking.php',
    'register.php', 
    'register_action.php'
];

if (!in_array($current_page, $allowed_pages) && !isset($_SESSION['user_id'])) {
    $path = (strpos($_SERVER['PHP_SELF'], 'pages/') !== false) ? '../../login.php' : 'login.php';
    header("Location: $path");
    exit();
}
?>