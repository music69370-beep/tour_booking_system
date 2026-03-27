<?php
include 'config/db.php';

if (isset($_POST['code'])) {
    $code = strtoupper(mysqli_real_escape_string($conn, $_POST['code']));
    $tour_id = $_POST['tour_id'];
    $subtotal = $_POST['subtotal'];
    $phone = $_POST['phone']; // ໃຊ້ເບີໂທເພື່ອກວດສອບສິດ/ຄົນ
    $today = date('Y-m-d');

    // 1. ດຶງຂໍ້ມູນຄູປອງ
    $sql = "SELECT * FROM coupons WHERE code = '$code' AND status = 'Active' AND expiry_date >= '$today'";
    $res = mysqli_query($conn, $sql);
    $coupon = mysqli_fetch_assoc($res);

    if (!$coupon) {
        echo json_encode(['status' => 'error', 'message' => 'ລະຫັດບໍ່ຖືກຕ້ອງ ຫຼື ໝົດອາຍຸ']);
        exit;
    }

    // 2. ກວດສອບຍອດຊື້ຂັ້ນຕ່ຳ
    if ($subtotal < $coupon['min_spend']) {
        echo json_encode(['status' => 'error', 'message' => 'ຍອດຈອງຕ້ອງຮອດ '.number_format($coupon['min_spend']).' ກີບ']);
        exit;
    }

    // 3. ກວດສອບວ່າໃຊ້ກັບທົວນີ້ໄດ້ບໍ່
    if ($coupon['specific_tour_id'] !== NULL && $coupon['specific_tour_id'] != $tour_id) {
        echo json_encode(['status' => 'error', 'message' => 'ລະຫັດນີ້ໃຊ້ບໍ່ໄດ້ກັບແພັກເກັດນີ້']);
        exit;
    }

    // 4. ກວດສອບສິດທັງໝົດ (Total Limit)
    $cid = $coupon['coupon_id'];
    $used_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE coupon_id = $cid AND status != 'Cancelled'"))['c'];
    if ($coupon['total_limit'] > 0 && $used_total >= $coupon['total_limit']) {
        echo json_encode(['status' => 'error', 'message' => 'ສິດເຕັມແລ້ວ']);
        exit;
    }

    // 5. ກວດສອບສິດ/ຄົນ (Limit per User)
    $used_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings b JOIN customers c ON b.customer_id = c.customer_id WHERE b.coupon_id = $cid AND c.phone = '$phone' AND b.status != 'Cancelled'"))['c'];
    if ($used_user >= $coupon['limit_per_user']) {
        echo json_encode(['status' => 'error', 'message' => 'ທ່ານໃຊ້ລະຫັດນີ້ຄົບຕາມສິດແລ້ວ']);
        exit;
    }

    // 6. ຄຳນວນສ່ວນຫຼຸດແທ້
    $discount = 0;
    if ($coupon['discount_type'] == 'Fixed') {
        $discount = $coupon['discount_value'];
    } else {
        $discount = ($subtotal * $coupon['discount_value']) / 100;
        if ($coupon['max_discount'] > 0 && $discount > $coupon['max_discount']) {
            $discount = $coupon['max_discount'];
        }
    }

    echo json_encode([
        'status' => 'success',
        'discount' => $discount,
        'id' => $cid
    ]);
    exit;
}