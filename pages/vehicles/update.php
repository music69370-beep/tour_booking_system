<?php
include '../../config/db.php';

if (isset($_POST['update_vehicle'])) {
    $id = $_POST['vehicle_id'];
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $plate = mysqli_real_escape_string($conn, $_POST['plate_number']);
    $type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $cap = $_POST['capacity'];
    $ins_expiry = $_POST['insurance_expiry'];
    $driver = mysqli_real_escape_string($conn, $_POST['driver_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['driver_phone']);
    $status = $_POST['status'];

    $upload_path = "../../assets/uploads/vehicles/";
    
    // ຈັດການຮູບຄົນຂັບ
    if (!empty($_FILES['driver_image']['name'])) {
        $d_img = time() . "_driver_" . $_FILES['driver_image']['name'];
        move_uploaded_file($_FILES['driver_image']['tmp_name'], $upload_path . $d_img);
        // ລຶບຮູບເກົ່າ
        if (!empty($_POST['old_driver_image']) && file_exists($upload_path . $_POST['old_driver_image'])) {
            unlink($upload_path . $_POST['old_driver_image']);
        }
    } else {
        $d_img = $_POST['old_driver_image'];
    }

    $sql = "UPDATE vehicles SET 
            plate_number = '$plate', 
            model = '$model', 
            vehicle_type = '$type', 
            capacity = '$cap', 
            insurance_expiry = '$ins_expiry', 
            driver_name = '$driver', 
            driver_phone = '$phone', 
            driver_image = '$d_img',
            status = '$status' 
            WHERE vehicle_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=updated");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>