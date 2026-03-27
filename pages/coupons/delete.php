<?php
include '../../config/db.php';
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    if (mysqli_query($conn, "DELETE FROM coupons WHERE coupon_id = '$id'")) {
        header("Location: index.php?msg=deleted");
    }
}
?>