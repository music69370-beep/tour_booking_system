<?php
include '../../config/db.php';

// ກວດສອບສິດ (ຕ້ອງເປັນ Admin ເທົ່ານັ້ນຈຶ່ງລຶບໄດ້)
if (!isAdmin()) {
    header("Location: ../dashboard/index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. ປ້ອງກັນບໍ່ໃຫ້ລຶບ "ຕົວເອງ" (ຄົນທີ່ Login ຢູ່)
    if ($id == $_SESSION['user_id']) {
        header("Location: index.php?msg=error_self_delete");
        exit();
    }

    // 2. ດຳເນີນການລຶບ
    $sql = "DELETE FROM users WHERE user_id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=deleted");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
    exit();
}
?>