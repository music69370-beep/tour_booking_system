<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_guide'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $license_id = mysqli_real_escape_string($conn, $_POST['license_id']);
    $license_expiry = $_POST['license_expiry'];
    $languages = mysqli_real_escape_string($conn, $_POST['languages']);
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
    $exp_years = intval($_POST['exp_years']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
    $bank_account = mysqli_real_escape_string($conn, $_POST['bank_account']);
    $emergency_name = mysqli_real_escape_string($conn, $_POST['emergency_name']);
    $emergency_phone = mysqli_real_escape_string($conn, $_POST['emergency_phone']);
    $first_aid = isset($_POST['first_aid_certified']) ? 1 : 0;

    $upload_dir = "../../assets/uploads/guides/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    // 1. ຈັດການຮູບປະຈຳຕົວ
    $image_name = "";
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_guide_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
    }

    // 2. ຈັດການເອກະສານຕິດຄັດ
    $doc_name = "";
    if (!empty($_FILES['doc_attachment']['name'])) {
        $doc_name = time() . "_doc_" . $_FILES['doc_attachment']['name'];
        move_uploaded_file($_FILES['doc_attachment']['tmp_name'], $upload_dir . $doc_name);
    }

    $sql = "INSERT INTO guides (
        fullname, license_id, license_expiry, languages, specialization, exp_years, 
        phone, email, address, bank_name, bank_account, emergency_contact_name, 
        emergency_contact_phone, first_aid_certified, image, doc_attachment, status
    ) VALUES (
        '$fullname', '$license_id', '$license_expiry', '$languages', '$specialization', '$exp_years', 
        '$phone', '$email', '$address', '$bank_name', '$bank_account', '$emergency_name', 
        '$emergency_phone', '$first_aid', '$image_name', '$doc_name', 'Available'
    )";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>