<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['btn_save'])) {
    // 1. ຮັບຂໍ້ມູນຈາກຟອມ
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $id_card_no = mysqli_real_escape_string($conn, $_POST['id_card_no']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $emergency_phone = mysqli_real_escape_string($conn, $_POST['emergency_phone']);
    $license_number = mysqli_real_escape_string($conn, $_POST['license_number']);
    $license_type = $_POST['license_type'];
    $license_expiry = $_POST['license_expiry'];
    $experience_years = intval($_POST['experience_years']);

    // 2. ຈັດການ Folder
    $upload_dir = "../../assets/uploads/drivers/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // ຈັດການອັບໂຫລດ 3 ຮູບ
    $image_name = "";
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_driver_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
    }

    $license_image_name = "";
    if (!empty($_FILES['license_image']['name'])) {
        $license_image_name = time() . "_license_" . $_FILES['license_image']['name'];
        move_uploaded_file($_FILES['license_image']['tmp_name'], $upload_dir . $license_image_name);
    }

    $id_card_image_name = "";
    if (!empty($_FILES['id_card_image']['name'])) {
        $id_card_image_name = time() . "_idcard_" . $_FILES['id_card_image']['name'];
        move_uploaded_file($_FILES['id_card_image']['tmp_name'], $upload_dir . $id_card_image_name);
    }

    // 3. ບັນທຶກ SQL (ເພີ່ມ id_card_image)
    $sql = "INSERT INTO drivers (
                fullname, phone, id_card_no, address, emergency_phone, 
                license_number, license_type, license_expiry, experience_years, 
                image, license_image, id_card_image, status
            ) VALUES (
                '$fullname', '$phone', '$id_card_no', '$address', '$emergency_phone', 
                '$license_number', '$license_type', '$license_expiry', $experience_years, 
                '$image_name', '$license_image_name', '$id_card_image_name', 'Available'
            )";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>