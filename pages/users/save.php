<?php
include '../../config/db.php';

if (isset($_POST['save_user'])) {
    // 1. ຮັບຂໍ້ມູນພື້ນຖານ
    $code = mysqli_real_escape_string($conn, $_POST['employee_code']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $dob = $_POST['dob'];
    
    // 2. ຮັບຂໍ້ມູນການງານ
    $job = mysqli_real_escape_string($conn, $_POST['job_title']);
    $dept = mysqli_real_escape_string($conn, $_POST['department']);
    $joined = $_POST['date_joined'];
    
    // 3. ຮັບຂໍ້ມູນເອກະສານ & ສຸກເສີນ
    $id_card = mysqli_real_escape_string($conn, $_POST['id_card_no']);
    $e_name = mysqli_real_escape_string($conn, $_POST['emergency_name']);
    $e_phone = mysqli_real_escape_string($conn, $_POST['emergency_phone']);
    
    // 4. ຮັບຂໍ້ມູນ Security
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 5. ກວດສອບ Username ຊ້ຳ
    $check_user = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check_user) > 0) {
        header("Location: add.php?msg=duplicate");
        exit();
    }

    // 6. ຈັດການຮູບພາບ
    $pic_name = "";
    if (!empty($_FILES['profile_pic']['name'])) {
        $pic_name = time() . "_user_" . $_FILES['profile_pic']['name'];
        if (!is_dir("../../assets/uploads/users/")) mkdir("../../assets/uploads/users/", 0777, true);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], "../../assets/uploads/users/" . $pic_name);
    }

    // 7. ບັນທຶກລົງ Database
    $sql = "INSERT INTO users (
        employee_code, username, password, fullname, phone, email, address, 
        dob, profile_pic, date_joined, job_title, department, id_card_no, 
        emergency_name, emergency_phone, role, status
    ) VALUES (
        '$code', '$username', '$password', '$fullname', '$phone', '$email', '$address', 
        '$dob', '$pic_name', '$joined', '$job', '$dept', '$id_card', 
        '$e_name', '$e_phone', '$role', 'Active'
    )";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>