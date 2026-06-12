<?php
include '../../config/db.php';
/** @var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $birthday = mysqli_real_escape_string($conn, $_POST['birthday']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
    $id_card_no = mysqli_real_escape_string($conn, $_POST['id_card_no']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $emergency_name = mysqli_real_escape_string($conn, $_POST['emergency_name'] ?? '');
    $emergency_phone = mysqli_real_escape_string($conn, $_POST['emergency_phone'] ?? '');

    // 1. ກວດສອບເບີໂທຊ້ຳ
    $check = mysqli_query($conn, "SELECT customer_id FROM customers WHERE phone = '$phone'");
    if (mysqli_num_rows($check) > 0) {
        $exist = mysqli_fetch_assoc($check);
        echo json_encode(['status' => 'success', 'customer_id' => $exist['customer_id']]);
        exit();
    }

    // 2. ຈັດການຮູບ
    $img_name = "";
    if (!empty($_FILES['id_card_image']['name'])) {
        $upload_dir = "../../assets/uploads/customers/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $img_name = time() . "_" . basename($_FILES['id_card_image']['name']);
        move_uploaded_file($_FILES['id_card_image']['tmp_name'], $upload_dir . $img_name);
    }

    // 3. ບັນທຶກ (ກວດສອບຊື່ Column ໃຫ້ຕົງກັບທີ່ ALTER TABLE ໄປ)
    $sql = "INSERT INTO customers (fullname, gender, birthday, nationality, id_card_no, id_card_image, phone, email, address, emergency_name, emergency_phone) 
            VALUES ('$fullname', '$gender', '$birthday', '$nationality', '$id_card_no', '$img_name', '$phone', '$email', '$address', '$emergency_name', '$emergency_phone')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'customer_id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
}
?>