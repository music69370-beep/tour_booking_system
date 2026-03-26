<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$conn = mysqli_connect("localhost", "root", "", "tour_booking_db");
mysqli_set_charset($conn, "utf8mb4");

if (!$conn) die("Database Connection Failed");

define('BASE_URL', 'http://localhost/tour_booking_system/');
?>