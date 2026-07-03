<?php
require_once 'config/db.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $query = mysqli_query($conn, $sql);

    if ($query && mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            // ແຍກທາງໄປຕາມສິດ
            if ($user['role'] === 'Admin') {
                header("Location: pages/dashboard/index.php");
            } else {
                header("Location: pages/bookings/index.php");
            }
            exit();
        } else {
            header("Location: login.php?error=wrong_password");
            exit();
        }
    } else {
        header("Location: login.php?error=user_not_found");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>