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

// --- ລະບົບປ່ຽນພາສາ ---
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// ຖ້າຍັງບໍ່ເລືອກພາສາ ໃຫ້ເປັນພາສາລາວ (lao) ໂດຍເລີ່ມຕົ້ນ
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'lao';

if ($current_lang == 'eng') {
    include 'lang_eng.php';
} else {
    include 'lang_lao.php';
}

// ຄວາມປອດໄພ (Allowed Pages)
$current_page = basename($_SERVER['PHP_SELF']);
$frontend_pages = ['index.php', 'booking_form.php', 'process_booking.php', 'checkout.php', 'check_status.php', 'save_review.php', 'login.php', 'auth_action.php', 'register.php', 'register_action.php', 'setup_db.php'];

if (!in_array($current_page, $frontend_pages) && !isset($_SESSION['user_id'])) {
    $redirect_path = (strpos($_SERVER['PHP_SELF'], 'pages/') !== false) ? '../../login.php' : 'login.php';
    header("Location: $redirect_path");
    exit();
}
?>