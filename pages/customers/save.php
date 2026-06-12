<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_customer'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $id_card_no = mysqli_real_escape_string($conn, $_POST['id_card_no']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $emergency_name = mysqli_real_escape_string($conn, $_POST['emergency_name']);
    $emergency_phone = mysqli_real_escape_string($conn, $_POST['emergency_phone']);

    // --- Logic ຈັດການອັບໂຫລດຮູບ ---
    $id_card_image_name = "";
    if (!empty($_FILES['id_card_image']['name'])) {
        $upload_dir = "../../assets/uploads/customers/";
        
        // ສ້າງ Folder ຖ້າຍັງບໍ່ມີ
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // ຕັ້ງຊື່ຮູບໃໝ່ປ້ອງກັນຊື່ຊ້ຳ (timestamp + original name)
        $id_card_image_name = time() . "_" . basename($_FILES['id_card_image']['name']);
        $target_file = $upload_dir . $id_card_image_name;

        // ຍ້າຍໄຟລ໌ຈາກ Temp ໄປບ່ອນເກັບແທ້
        move_uploaded_file($_FILES['id_card_image']['tmp_name'], $target_file);
    }

    $sql = "INSERT INTO customers (
                fullname, gender, birthday, phone, id_card_no, id_card_image,
                nationality, email, address, emergency_name, emergency_phone
            ) VALUES (
                '$fullname', '$gender', '$birthday', '$phone', '$id_card_no', '$id_card_image_name',
                '$nationality', '$email', '$address', '$emergency_name', '$emergency_phone'
            )";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>