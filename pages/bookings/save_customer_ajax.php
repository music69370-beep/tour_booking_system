<?php
include '../../config/db.php';
/** @var mysqli $conn */

// ປິດ Error ເພື່ອໃຫ້ JSON ສະອາດ
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $gender   = mysqli_real_escape_string($conn, $_POST['gender']);
    $birthday = mysqli_real_escape_string($conn, $_POST['birthday']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
    $id_card_no  = mysqli_real_escape_string($conn, trim($_POST['id_card_no']));
    $phone    = mysqli_real_escape_string($conn, str_replace(' ', '', $_POST['phone']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $address  = mysqli_real_escape_string($conn, trim($_POST['address']));
    $emergency_name  = mysqli_real_escape_string($conn, trim($_POST['emergency_name']));
    $emergency_phone = mysqli_real_escape_string($conn, trim($_POST['emergency_phone']));

    // 1. ຈັດການຮູບພາບ
    $img_name = "";
    if (!empty($_FILES['id_card_image']['name'])) {
        $upload_dir = "../../assets/uploads/customers/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $ext = pathinfo($_FILES['id_card_image']['name'], PATHINFO_EXTENSION);
        $img_name = time() . "_" . rand(100,999) . "." . $ext;
        move_uploaded_file($_FILES['id_card_image']['tmp_name'], $upload_dir . $img_name);
    }

    // 2. ກວດສອບເບີໂທຊ້ຳ (ຖ້າມີແລ້ວໃຫ້ Update ຂໍ້ມູນໃໝ່ທັບ)
    $check_res = mysqli_query($conn, "SELECT customer_id, id_card_image FROM customers WHERE phone = '$phone'");
    
    if (mysqli_num_rows($check_res) > 0) {
        $exist = mysqli_fetch_assoc($check_res);
        $cid = $exist['customer_id'];
        if ($img_name == "") $img_name = $exist['id_card_image'];

        $sql = "UPDATE customers SET 
                fullname='$fullname', gender='$gender', birthday='$birthday', 
                nationality='$nationality', id_card_no='$id_card_no', id_card_image='$img_name', 
                email='$email', address='$address', emergency_name='$emergency_name', 
                emergency_phone='$emergency_phone' 
                WHERE customer_id = '$cid'";
    } else {
        $sql = "INSERT INTO customers (fullname, gender, birthday, nationality, id_card_no, id_card_image, phone, email, address, emergency_name, emergency_phone) 
                VALUES ('$fullname', '$gender', '$birthday', '$nationality', '$id_card_no', '$img_name', '$phone', '$email', '$address', '$emergency_name', '$emergency_phone')";
    }

    if (mysqli_query($conn, $sql)) {
        $res_id = (isset($cid)) ? $cid : mysqli_insert_id($conn);
        echo json_encode(['status' => 'success', 'customer_id' => $res_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
}
?>