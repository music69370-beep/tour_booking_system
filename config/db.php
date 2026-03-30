<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
$conn = mysqli_connect("localhost", "root", "", "tour_booking_db");
mysqli_set_charset($conn, "utf8mb4");

if (!$conn) die("Database Connection Failed");

// 2. ກຳນົດ URL ພື້ນຖານ (ປັບໃຫ້ກົງກັບ Folder ຂອງທ່ານ)
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/tour_booking_system/');
}

// 3. ລະບົບປ່ຽນພາສາ (Lao / English)
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'lao';

// ດຶງໄຟລ໌ແປພາສາ
if ($current_lang == 'eng') {
    include 'lang_eng.php';
} else {
    include 'lang_lao.php';
}

// 4. ລະບົບຄວາມປອດໄພ (Security Control)
$current_page = basename($_SERVER['PHP_SELF']);

// ລາຍຊື່ໜ້າເວັບທີ່ "ລູກຄ້າ" ຫຼື "ບຸກຄົນທົ່ວໄປ" ເຂົ້າເຖິງໄດ້ໂດຍບໍ່ຕ້ອງ Login
$frontend_pages = [
    'index.php', 
    'booking_form.php', 
    'process_booking.php', 
    'checkout.php', 
    'check_status.php', 
    'save_review.php', 
    'ticket.php',           // <--- ໜ້າໃບຢັ້ງຢືນການຈອງໃໝ່ສຳລັບລູກຄ້າ
    'login.php', 
    'auth_action.php', 
    'register.php', 
    'register_action.php', 
    'setup_db.php'
];

// ກວດສອບສິດ: ຖ້າພະຍາຍາມເຂົ້າໜ້າໃນ Folder pages/ ໂດຍບໍ່ໄດ້ Login ໃຫ້ດີດໄປໜ້າ Login ທັນທີ
if (!in_array($current_page, $frontend_pages) && !isset($_SESSION['user_id'])) {
    // ກວດເຊັກວ່າໄຟລ໌ຢູ່ໃນ Folder ຍ່ອຍຫຼືບໍ່
    $is_subfolder = (strpos($_SERVER['PHP_SELF'], 'pages/') !== false);
    $redirect_path = $is_subfolder ? '../../login.php' : 'login.php';
    header("Location: $redirect_path");
    exit();
}
?>