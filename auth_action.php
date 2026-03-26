<?php
// ຕ້ອງເອີ້ນໃຊ້ db.php ກ່ອນ
require_once 'config/db.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $query = mysqli_query($conn, $sql);
    
    if ($query && mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        
        // ກວດສອບລະຫັດຜ່ານ (ສົມທຽບຂໍ້ຄວາມທຳມະດາຕາມທີ່ເຈົ້າເພີ່ມໃນ SQL)
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            
            // Login ສຳເລັດ ເດັ້ງໄປ Dashboard
            header("Location: pages/dashboard/index.php");
            exit();
        } else {
            // ລະຫັດຜ່ານຜິດ
            header("Location: login.php?error=wrong_password");
            exit();
        }
    } else {
        // ບໍ່ພົບຊື່ຜູ້ໃຊ້
        header("Location: login.php?error=user_not_found");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>