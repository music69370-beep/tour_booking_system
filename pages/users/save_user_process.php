<?php
include '../../config/db.php';

if (isset($_POST['save_user'])) {
    $user_id = $_POST['user_id'];
    $employee_code = mysqli_real_escape_string($conn, $_POST['employee_code']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $dob = $_POST['dob'];
    $id_card_no = mysqli_real_escape_string($conn, $_POST['id_card_no']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    // --- 1. ກວດສອບຂໍ້ມູນຊ້ຳ (Employee Code) ---
    $check_code_q = empty($user_id) ? "employee_code = '$employee_code'" : "employee_code = '$employee_code' AND user_id != '$user_id'";
    $check_code = mysqli_query($conn, "SELECT user_id FROM users WHERE $check_code_q");
    if (mysqli_num_rows($check_code) > 0) {
        header("Location: index.php?msg=duplicate_code");
        exit();
    }

    // --- 2. ກວດສອບ Username ຊ້ຳ ---
    $check_user_q = empty($user_id) ? "username = '$username'" : "username = '$username' AND user_id != '$user_id'";
    $check_user = mysqli_query($conn, "SELECT user_id FROM users WHERE $check_user_q");
    if (mysqli_num_rows($check_user) > 0) {
        header("Location: index.php?msg=duplicate_user");
        exit();
    }

    // --- 3. ຈັດການຮູບພາບ ---
    $target_dir = "../../assets/uploads/users/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    // ຮູບໂປຣຟາຍ
    $pic_name = $_POST['old_profile_pic'];
    if (!empty($_FILES['profile_pic']['name'])) {
        $pic_name = "prof_" . time() . "_" . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_dir . $pic_name);
    }

    // ຮູບພາບບັດປະຈຳໂຕ
    $id_card_img = $_POST['old_id_card_img'];
    if (!empty($_FILES['id_card_img']['name'])) {
        $id_card_img = "idcard_" . time() . "_" . basename($_FILES['id_card_img']['name']);
        move_uploaded_file($_FILES['id_card_img']['tmp_name'], $target_dir . $id_card_img);
    }

    // --- 4. ບັນທຶກຂໍ້ມູນ ---
    if (empty($user_id)) {
        // ກໍລະນີ ເພີ່ມໃໝ່
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (employee_code, username, password, fullname, phone, email, dob, id_card_no, address, profile_pic, id_card_img, role, status) 
                VALUES ('$employee_code', '$username', '$password', '$fullname', '$phone', '$email', '$dob', '$id_card_no', '$address', '$pic_name', '$id_card_img', '$role', '$status')";
    } else {
        // ກໍລະນີ ແກ້ໄຂ
        $sql = "UPDATE users SET 
                employee_code='$employee_code', username='$username', fullname='$fullname', phone='$phone', 
                email='$email', dob='$dob', id_card_no='$id_card_no', address='$address', 
                profile_pic='$pic_name', id_card_img='$id_card_img', role='$role', status='$status'";
        
        if (!empty($_POST['password'])) {
            $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql .= ", password='$new_pass'";
        }
        $sql .= " WHERE user_id = '$user_id'";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>