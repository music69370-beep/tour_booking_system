<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
$conn = mysqli_connect("localhost", "root", "", "tour_booking_db");
mysqli_set_charset($conn, "utf8mb4");

if (!$conn) die("Database Connection Failed");

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

// ໜ້າທີ່ລູກຄ້າເຂົ້າໄດ້ໂດຍບໍ່ຕ້ອງ Login
$frontend_pages = ['index.php', 'booking_form.php', 'process_booking.php', 'checkout.php', 'check_status.php', 'save_review.php', 'ticket.php', 'login.php', 'auth_action.php', 'register.php', 'register_action.php', 'setup_db.php'];

if (!in_array($current_page, $frontend_pages) && !isset($_SESSION['user_id'])) {
    $is_sub = (strpos($current_path, 'pages/') !== false);
    header("Location: " . ($is_sub ? '../../login.php' : 'login.php'));
    exit();
}

// *** ຈຳກັດສິດ Staff: ຫ້າມເຂົ້າ Folder ຜູ້ໃຊ້, ການເງິນ ແລະ ຄູປອງ ***
if (isset($_SESSION['role']) && $_SESSION['role'] === 'Staff') {
    if (strpos($current_path, 'pages/users/') !== false || 
        strpos($current_path, 'pages/coupons/') !== false || 
        strpos($current_path, 'pages/payments/') !== false) {
        
        // ຖ້າ Staff ພະຍາຍາມເຂົ້າ ໃຫ້ດີດໄປໜ້າ Dashboard
        header("Location: " . (strpos($current_path, 'pages/') !== false ? '../dashboard/index.php' : 'pages/dashboard/index.php'));
        exit();
    }
}
?>