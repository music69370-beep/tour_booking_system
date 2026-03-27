<?php
include '../../config/db.php';

if (isset($_POST['save_coupon'])) {
    $code = strtoupper(mysqli_real_escape_string($conn, $_POST['code']));
    $amount = mysqli_real_escape_string($conn, $_POST['discount_amount']);
    $expiry = $_POST['expiry_date'];

    $sql = "INSERT INTO coupons (code, discount_amount, expiry_date, status) 
            VALUES ('$code', '$amount', '$expiry', 'Active')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>