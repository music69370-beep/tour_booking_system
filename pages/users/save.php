<?php
include '../../config/db.php';

if (isset($_POST['save_user'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];
    
    // ເຂົ້າລະຫັດ Password ເພື່ອຄວາມປອດໄພ
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (fullname, username, password, role) 
            VALUES ('$fullname', '$username', '$password', '$role')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>