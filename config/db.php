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

// --- ລະບົບພາສາ ---
if (isset($_GET['lang'])) { $_SESSION['lang'] = $_GET['lang']; }
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'lao';
include ($current_lang == 'eng') ? 'lang_eng.php' : 'lang_lao.php';

// --- ຟັງຊັນກວດສອບສິດ (Helper Functions) ---
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

function isStaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Staff';
}

// ຄວາມປອດໄພພື້ນຖານ
$current_page = basename($_SERVER['PHP_SELF']);
$frontend_pages = ['index.php', 'booking_form.php', 'process_booking.php', 'checkout.php', 'check_status.php', 'save_review.php', 'ticket.php', 'login.php', 'auth_action.php', 'register.php', 'register_action.php', 'setup_db.php'];

if (!in_array($current_page, $frontend_pages) && !isset($_SESSION['user_id'])) {
    $redirect_path = (strpos($_SERVER['PHP_SELF'], 'pages/') !== false) ? '../../login.php' : 'login.php';
    header("Location: $redirect_path");
    exit();
}
?>