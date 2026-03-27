<?php
include '../../config/db.php';

if (isset($_POST['update_guide'])) {
    $id = $_POST['guide_id'];
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $license_id = mysqli_real_escape_string($conn, $_POST['license_id']);
    $license_expiry = $_POST['license_expiry'];
    $languages = mysqli_real_escape_string($conn, $_POST['languages']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $bank_name = $_POST['bank_name'];
    $bank_account = mysqli_real_escape_string($conn, $_POST['bank_account']);
    $status = $_POST['status'];

    $path = "../../assets/uploads/guides/";
    
    // ຈັດການຮູບ
    if (!empty($_FILES['image']['name'])) {
        $img_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $path . $img_name);
        if ($_POST['old_image'] && file_exists($path . $_POST['old_image'])) unlink($path . $_POST['old_image']);
    } else {
        $img_name = $_POST['old_image'];
    }

    $sql = "UPDATE guides SET 
            fullname='$fullname', license_id='$license_id', license_expiry='$license_expiry',
            languages='$languages', phone='$phone', bank_name='$bank_name',
            bank_account='$bank_account', image='$img_name', status='$status'
            WHERE guide_id='$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=updated");
    }
}
?>