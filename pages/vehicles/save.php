<?php
include '../../config/db.php';

if (isset($_POST['save_vehicle'])) {
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $plate = mysqli_real_escape_string($conn, $_POST['plate_number']);
    $cap = $_POST['capacity'];
    $driver = mysqli_real_escape_string($conn, $_POST['driver_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['driver_phone']);

    $sql = "INSERT INTO vehicles (model, plate_number, capacity, driver_name, driver_phone, status) 
            VALUES ('$model', '$plate', '$cap', '$driver', '$phone', 'Available')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
    }
}
?>