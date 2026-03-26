<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$conn = mysqli_connect("localhost", "root", "", "tour_booking_db");
mysqli_set_charset($conn, "utf8mb4");

if (!$conn) die("Database Connection Failed");

define('BASE_URL', 'http://localhost/tour_booking_system/');
// ກວດສອບວ່າ ຖ້າບໍ່ແມ່ນໜ້າ login.php ແລະ ຍັງບໍ່ທັນ Login ໃຫ້ເດັ້ງໄປໜ້າ Login
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page != 'login.php' && $current_page != 'setup_db.php' && !isset($_SESSION['user_id'])) {
    // ຖ້າຢູ່ໃສ Folder ຍ່ອຍ ໃຫ້ຖອຍອອກໄປຫາ Root
    $path = (strpos($_SERVER['PHP_SELF'], 'pages/') !== false) ? '../../login.php' : 'login.php';
    header("Location: $path");
    exit();
}
?>