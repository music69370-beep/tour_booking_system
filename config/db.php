<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 0. ການຈັດການ Error
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// 1. ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
$conn = mysqli_connect("localhost", "tour_admin", "123", "tour_booking_db");
mysqli_set_charset($conn, "utf8mb4");

if (!$conn) die("Database Connection Failed");

require_once __DIR__ . '/../includes/functions.php';

// 2. ກຳນົດ URL ພື້ນຖານ
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/tour_booking_system/');
}

// 3. ລະບົບປ່ຽນພາສາ
if (isset($_GET['lang'])) { $_SESSION['lang'] = $_GET['lang']; }
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'lao';
$lang_file = (strpos($_SERVER['PHP_SELF'], 'pages/') !== false) ? '../../config/' : 'config/';
include_once $lang_file . ($current_lang == 'eng' ? 'lang_eng.php' : 'lang_lao.php');

// 4. ຟັງຊັນກວດສອບສິດ
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

// 5. ລະບົບຄວາມປອດໄພ (Guard)
$current_page = basename($_SERVER['PHP_SELF']);
$current_path = $_SERVER['PHP_SELF'];

$frontend_pages = [
    'index.php', 'booking_form.php', 'process_booking.php', 'checkout.php', 
    'check_status.php', 'save_review.php', 'ticket.php', 'login.php', 
    'auth_action.php', 'register.php', 'register_action.php', 'setup_db.php',
    'get_occupied_seats.php'
];

// ອະນຸຍາດໃຫ້ໄຟລ໌ບັນທຶກເງິນ ເຂົ້າເຖິງໄດ້ (ສຳລັບລູກຄ້າສົ່ງສະລິບ)
$is_payment_save = (strpos($current_path, 'pages/payments/save.php') !== false);

if (!in_array($current_page, $frontend_pages) && !$is_payment_save && !isset($_SESSION['user_id'])) {
    $is_sub = (strpos($current_path, 'pages/') !== false);
    header("Location: " . ($is_sub ? '../../login.php' : 'login.php'));
    exit();
}

// ການຈຳກັດສິດ Staff (Lock ໃຫ້ຢູ່ແຕ່ໜ້າການຈອງ, ລູກຄ້າ, ຮັບເງິນ)
if (isset($_SESSION['role']) && $_SESSION['role'] === 'Staff') {
    $allowed_paths = [
        'pages/bookings/', 'pages/customers/', 'pages/payments/', 'logout.php'
    ];
    $can_access = false;
    foreach ($allowed_paths as $path) {
        if (strpos($current_path, $path) !== false) { $can_access = true; break; }
    }
    if (!$can_access && !in_array($current_page, $frontend_pages)) {
        header("Location: " . BASE_URL . "pages/bookings/index.php");
        exit();
    }
}
?>