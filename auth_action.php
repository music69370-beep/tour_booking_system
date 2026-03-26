<?php
include 'config/db.php';

if(isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    // ໃນທີ່ນີ້ເຮົາຈະເຊັກແບບງ່າຍໆກ່ອນ (ຖ້າໃຊ້ງານແທ້ຄວນໃຊ້ password_hash)
    if($user && $password == $user['password']) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['fullname'] = $user['fullname'];
        header("Location: pages/dashboard/index.php");
    } else {
        header("Location: login.php?error=1");
    }
}
?>