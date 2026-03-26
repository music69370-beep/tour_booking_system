<?php
// ດຶງໄຟລ໌ db.php ມາໃຊ້ (ເຊິ່ງມີ session_start() ແລະ ການເຊື່ອມຕໍ່ DB ແລ້ວ)
require_once 'config/db.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // ຄົ້ນຫາຂໍ້ມູນຜູ້ໃຊ້
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $query = mysqli_query($conn, $sql);

    if ($query && mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);

        // ກວດສອບລະຫັດຜ່ານ (ສົມທຽບລະຫັດທີ່ພິມມາ ກັບ ລະຫັດທີ່ເຂົ້າລະຫັດໄວ້ໃນ DB)
        if (password_verify($password, $user['password'])) {
            
            // ບັນທຶກຂໍ້ມູນຜູ້ໃຊ້ລົງໃນ Session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            // ເຂົ້າລະບົບສຳເລັດ -> ໄປໜ້າ Dashboard
            header("Location: pages/dashboard/index.php");
            exit();
        } else {
            // ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ
            header("Location: login.php?error=wrong_password");
            exit();
        }
    } else {
        // ບໍ່ພົບຊື່ຜູ້ໃຊ້ນີ້
        header("Location: login.php?error=user_not_found");
        exit();
    }
} else {
    // ຖ້າບໍ່ໄດ້ມາຈາກການກົດປຸ່ມ Login ໃຫ້ສົ່ງກັບ
    header("Location: login.php");
    exit();
}
?>