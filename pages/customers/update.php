<?php
include '../../config/db.php';

if (isset($_POST['update_customer'])) {
    $id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "UPDATE customers SET 
            fullname = '$fullname', 
            phone = '$phone', 
            email = '$email', 
            address = '$address' 
            WHERE customer_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=updated");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>