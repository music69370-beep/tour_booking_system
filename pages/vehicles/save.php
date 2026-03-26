<?php
include '../../config/db.php';

if (isset($_POST['save_vehicle'])) {
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $plate = mysqli_real_escape_string($conn, $_POST['plate_number']);
    $type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $cap = $_POST['capacity'];
    $ins_expiry = $_POST['insurance_expiry'];
    $amenities = mysqli_real_escape_string($conn, $_POST['amenities']);
    $driver = mysqli_real_escape_string($conn, $_POST['driver_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['driver_phone']);
    $lic_num = mysqli_real_escape_string($conn, $_POST['license_number']);
    $lic_expiry = $_POST['license_expiry'];
    $exp_years = $_POST['experience_years'];
    $emergency = mysqli_real_escape_string($conn, $_POST['emergency_contact']);

    // --- ຈຸດແກ້ໄຂ: ກວດສອບ ແລະ ສ້າງ Folder ຖ້າຍັງບໍ່ມີ ---
    $upload_dir = "../../assets/uploads/vehicles/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $d_img = "";
    if (!empty($_FILES['driver_image']['name'])) {
        $d_img = time() . "_driver_" . $_FILES['driver_image']['name'];
        move_uploaded_file($_FILES['driver_image']['tmp_name'], $upload_dir . $d_img);
    }

    $l_img = "";
    if (!empty($_FILES['license_image']['name'])) {
        $l_img = time() . "_license_" . $_FILES['license_image']['name'];
        move_uploaded_file($_FILES['license_image']['tmp_name'], $upload_dir . $l_img);
    }

    $sql = "INSERT INTO vehicles (
        plate_number, model, vehicle_type, capacity, insurance_expiry, amenities, 
        driver_name, driver_phone, license_number, license_expiry, experience_years, 
        emergency_contact, driver_image, license_image, status
    ) VALUES (
        '$plate', '$model', '$type', '$cap', '$ins_expiry', '$amenities', 
        '$driver', '$phone', '$lic_num', '$lic_expiry', '$exp_years', 
        '$emergency', '$d_img', '$l_img', 'Available'
    )";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>