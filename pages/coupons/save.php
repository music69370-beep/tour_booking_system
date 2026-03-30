<?php
include '../../config/db.php';

if (isset($_POST['save_coupon'])) {
    $code = strtoupper(mysqli_real_escape_string($conn, $_POST['code']));
    $type = $_POST['discount_type'];
    $val = $_POST['discount_value'];
    $max = $_POST['max_discount'] ?: 0;
    $min = $_POST['min_spend'] ?: 0;
    $total_lim = $_POST['total_limit'] ?: 0;
    $user_lim = $_POST['limit_per_user'] ?: 1;
    $tour_id = !empty($_POST['specific_tour_id']) ? $_POST['specific_tour_id'] : 'NULL';
    $expiry = $_POST['expiry_date'];
    
    // ເຊັກສະຖານະຈາກ Checkbox/Switch
    $status = (isset($_POST['status']) && $_POST['status'] == 'Active') ? 'Active' : 'Inactive';

    // ກວດສອບລະຫັດຊ້ຳ
    $check = mysqli_query($conn, "SELECT code FROM coupons WHERE code = '$code'");
    if (mysqli_num_rows($check) > 0) {
        header("Location: add.php?msg=duplicate");
        exit();
    }

    $sql = "INSERT INTO coupons (code, discount_type, discount_value, min_spend, max_discount, total_limit, limit_per_user, specific_tour_id, expiry_date, status) 
            VALUES ('$code', '$type', '$val', '$min', '$max', '$total_lim', '$user_lim', $tour_id, '$expiry', '$status')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>