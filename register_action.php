<?php
require_once 'config/db.php';

if (isset($_POST['register'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. ກວດສອບວ່າ Password ຕົງກັນບໍ່
    if ($password !== $confirm_password) {
        header("Location: register.php?msg=pass_mismatch");
        exit();
    }

    // 2. ກວດສອບວ່າ Username ຊ້ຳບໍ່
    $check_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check_user) > 0) {
        header("Location: register.php?msg=user_exists");
        exit();
    }

    // 3. ເຂົ້າລະຫັດ Password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 4. ບັນທຶກລົງ Database
    $sql = "INSERT INTO users (username, password, fullname, role) 
            VALUES ('$username', '$hashed_password', '$fullname', 'Staff')";

    if (mysqli_query($conn, $sql)) {
        // --- ຈຸດທີ່ແກ້ໄຂ: ສົ່ງໄປໜ້າ Login ພ້ອມ Parameter msg=registered ---
        header("Location: login.php?msg=registered");
        exit();
    } else {
        header("Location: register.php?msg=error");
        exit();
    }
}
?>