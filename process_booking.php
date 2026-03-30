<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. ຮັບຂໍ້ມູນລູກຄ້າ ແລະ ຂໍ້ມູນທົ່ວໄປ
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $num_people = intval($_POST['num_people']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    
    // 2. ຮັບຂໍ້ມູນສ່ວນຫຼຸດ
    $coupon_id = !empty($_POST['coupon_id']) ? intval($_POST['coupon_id']) : "NULL";
    $discount_amount = !empty($_POST['discount_val']) ? floatval($_POST['discount_val']) : 0;

    // 3. ຄຳນວນລາຄາລວມ (ດຶງຈາກ DB ເພື່ອກັນການແກ້ໄຂລາຄາໜ້າເວັບ)
    $tour_query = mysqli_query($conn, "SELECT price FROM tours WHERE tour_id = '$tour_id'");
    $tour_data = mysqli_fetch_assoc($tour_query);
    $price_per_person = $tour_data['price'];
    $total_price = ($price_per_person * $num_people) - $discount_amount;
    if ($total_price < 0) $total_price = 0;

    // 4. ບັນທຶກ ຫຼື ອັບເດດຂໍ້ມູນລູກຄ້າ (ໃຊ້ເບີໂທເປັນຫຼັກ)
    $sql_customer = "INSERT INTO customers (fullname, phone, email) 
                     VALUES ('$fullname', '$phone', '$email') 
                     ON DUPLICATE KEY UPDATE fullname='$fullname', email='$email'";
    mysqli_query($conn, $sql_customer);
    
    $customer_id = mysqli_insert_id($conn);
    if ($customer_id == 0) { // ຖ້າເປັນລູກຄ້າເກົ່າ ໃຫ້ໄປດຶງ ID ມາ
        $res_c = mysqli_query($conn, "SELECT customer_id FROM customers WHERE phone = '$phone'");
        $customer_id = mysqli_fetch_assoc($res_c)['customer_id'];
    }

    // 5. ບັນທຶກຂໍ້ມູນການຈອງ (Bookings)
    $sql_book = "INSERT INTO bookings (customer_id, tour_id, coupon_id, travel_date, num_people, total_price, discount_amount, note, status) 
                 VALUES ($customer_id, $tour_id, $coupon_id, '$travel_date', $num_people, $total_price, $discount_amount, '$note', 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        $booking_id = mysqli_insert_id($conn);

        // 6. ບັນທຶກລາຍຊື່ຜູ້ຮ່ວມເດີນທາງ (ຊື່ ແລະ ເບີໂທ)
        if (isset($_POST['participant_names']) && is_array($_POST['participant_names'])) {
            $p_names = $_POST['participant_names'];
            $p_phones = $_POST['participant_phones'];

            foreach ($p_names as $index => $p_name) {
                $safe_p_name = mysqli_real_escape_string($conn, $p_name);
                $safe_p_phone = mysqli_real_escape_string($conn, $p_phones[$index]);
                
                if (!empty($safe_p_name)) {
                    mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_phone) 
                                         VALUES ('$booking_id', '$safe_p_name', '$safe_p_phone')");
                }
            }
        }

        // 7. ສ້າງລາຍການວຽກ (Checklist) ອັດຕະໂນມັດ 5 ຢ່າງ
        $default_tasks = [
            'ກວດເຊັກຍອດເງິນຊຳລະ',
            'ຈອງໂຮງແຮມ/ທີ່ພັກ',
            'ຕິດຕໍ່ລົດ ແລະ ຄົນຂັບ',
            'ມອບໝາຍໄກ້ຜູ້ນຳທ່ຽວ',
            'ກຽມນ້ຳດື່ມ ແລະ ອາຫານວ່າງ'
        ];
        foreach ($default_tasks as $task_label) {
            $safe_label = mysqli_real_escape_string($conn, $task_label);
            mysqli_query($conn, "INSERT INTO booking_tasks (booking_id, task_label, is_completed) VALUES ($booking_id, '$safe_label', 0)");
        }

        // 8. ໄປໜ້າ Checkout ເພື່ອສະແດງ QR Code
        header("Location: checkout.php?booking_id=$booking_id");
        exit();
    } else {
        echo "SQL Error: " . mysqli_error($conn);
    }
}
?>