<?php
include 'config/db.php';

if (isset($_POST['code'])) {
    $code = strtoupper(trim($_POST['code']));
    $tour_id = intval($_POST['tour_id']);
    $subtotal = (float) $_POST['subtotal'];
    $phone = trim($_POST['phone']); // ໃຊ້ເບີໂທເພື່ອກວດສອບສິດ/ຄົນ
    $today = date('Y-m-d');

    // 1. ດຶງຂໍ້ມູນຄູປອງ (prepared statement)
    $stmt = mysqli_prepare($conn, "SELECT * FROM coupons WHERE code = ? AND status = 'Active' AND expiry_date >= ?");
    mysqli_stmt_bind_param($stmt, "ss", $code, $today);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
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

    // 5. ກວດສອບສິດ/ຄົນ (Limit per User) - prepared statement
    $stmt_u = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM bookings b JOIN customers c ON b.customer_id = c.customer_id WHERE b.coupon_id = ? AND c.phone = ? AND b.status != 'Cancelled'");
    mysqli_stmt_bind_param($stmt_u, "is", $cid, $phone);
    mysqli_stmt_execute($stmt_u);
    $used_user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_u))['c'];
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