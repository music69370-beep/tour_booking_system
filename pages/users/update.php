<?php
include '../../config/db.php';

// ກວດສອບສິດ Admin
if (!isAdmin()) {
    header("Location: ../dashboard/index.php");
    exit();
}

if (isset($_POST['update_user'])) {
    $id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    // 1. ອັບເດດຂໍ້ມູນພື້ນຖານກ່ອນ
    $sql = "UPDATE users SET fullname = '$fullname', username = '$username', role = '$role' WHERE user_id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        
        // 2. ຖ້າລູກຄ້າປ້ອນ Password ໃໝ່ມາ ໃຫ້ເຂົ້າລະຫັດແລ້ວອັບເດດທັບ
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password = '$hashed_password' WHERE user_id = '$id'");
        }

        // 3. ຖ້າແກ້ໄຂຂໍ້ມູນ "ຕົວເອງ" ໃຫ້ອັບເດດ Session ນຳ
        if ($id == $_SESSION['user_id']) {
            $_SESSION['username'] = $username;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['role'] = $role;
        }

        header("Location: index.php?msg=updated");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>