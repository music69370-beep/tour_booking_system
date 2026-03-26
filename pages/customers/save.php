<?php
include '../../config/db.php';

if (isset($_POST['save_customer'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "INSERT INTO customers (fullname, phone, email, address) 
            VALUES ('$fullname', '$phone', '$email', '$address')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>