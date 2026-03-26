<?php
$host = "localhost"; $user = "root"; $pass = ""; $db = "tour_booking_db";
$conn = mysqli_connect($host, $user, $pass);
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4");
mysqli_select_db($conn, $db);

require_once 'schema.php'; // ດຶງ SQL ມາຈາກ schema.php

echo "<h2>Database Setup</h2>";
foreach ($tables as $name => $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "✅ Table <b>$name</b>: Ready!<br>";
    } else {
        echo "❌ Table <b>$name</b>: Error -> " . mysqli_error($conn) . "<br>";
    }
}
mysqli_close($conn);
?>