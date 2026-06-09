<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['btn_update'])) {
    $id = $_POST['driver_id'];
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $id_card = mysqli_real_escape_string($conn, $_POST['id_card_no']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $lic_no = mysqli_real_escape_string($conn, $_POST['license_number']);
    $lic_type = $_POST['license_type'];
    $lic_exp = $_POST['license_expiry'];
    $exp_years = intval($_POST['experience_years']);
    $status = $_POST['status'];

    $upload_dir = "../../assets/uploads/drivers/";

    // Logic ຈັດການ 3 ຮູບ (ຖ້າມີຮູບໃໝ່ ໃຫ້ລົບຮູບເກົ່າ)
    
    // 1. ຮູບຄົນຂັບ
    $img_name = $_POST['old_image'];
    if (!empty($_FILES['image']['name'])) {
        $img_name = time() . "_driver_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $img_name);
        if ($_POST['old_image'] && file_exists($upload_dir . $_POST['old_image'])) unlink($upload_dir . $_POST['old_image']);
    }

    // 2. ຮູບໃບຂັບຂີ່
    $lic_img_name = $_POST['old_license_image'];
    if (!empty($_FILES['license_image']['name'])) {
        $lic_img_name = time() . "_license_" . $_FILES['license_image']['name'];
        move_uploaded_file($_FILES['license_image']['tmp_name'], $upload_dir . $lic_img_name);
        if ($_POST['old_license_image'] && file_exists($upload_dir . $_POST['old_license_image'])) unlink($upload_dir . $_POST['old_license_image']);
    }

    // 3. ຮູບບັດປະຈຳຕົວ
    $id_img_name = $_POST['old_id_card_image'];
    if (!empty($_FILES['id_card_image']['name'])) {
        $id_img_name = time() . "_idcard_" . $_FILES['id_card_image']['name'];
        move_uploaded_file($_FILES['id_card_image']['tmp_name'], $upload_dir . $id_img_name);
        if ($_POST['old_id_card_image'] && file_exists($upload_dir . $_POST['old_id_card_image'])) unlink($upload_dir . $_POST['old_id_card_image']);
    }

    $sql = "UPDATE drivers SET 
            fullname='$fullname', phone='$phone', id_card_no='$id_card', 
            address='$address', license_number='$lic_no', license_type='$lic_type', 
            license_expiry='$lic_exp', experience_years=$exp_years, 
            image='$img_name', license_image='$lic_img_name', id_card_image='$id_img_name', status='$status'
            WHERE driver_id='$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=updated");
        exit();
    }
}
?>