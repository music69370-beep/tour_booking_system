<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $num_people = intval($_POST['num_people']);
    
    // --- ແກ້ໄຂບ່ອນນີ້: ປ້ອງກັນ Undefined Key ---
    $note = isset($_POST['note']) ? mysqli_real_escape_string($conn, $_POST['note']) : '';
    
    $coupon_id = !empty($_POST['coupon_id']) ? $_POST['coupon_id'] : 'NULL';
    $discount_amount = !empty($_POST['discount_val']) ? floatval($_POST['discount_val']) : 0;

    $tour_res = mysqli_query($conn, "SELECT price FROM tours WHERE tour_id = '$tour_id'");
    $tour_data = mysqli_fetch_assoc($tour_res);
    $price_per_person = $tour_data['price'];
    $total_price = ($price_per_person * $num_people) - $discount_amount;
    if ($total_price < 0) $total_price = 0;

    mysqli_query($conn, "INSERT INTO customers (fullname, phone, email) VALUES ('$fullname', '$phone', '$email') ON DUPLICATE KEY UPDATE fullname='$fullname', email='$email'");
    $customer_id = mysqli_insert_id($conn);
    
    if ($customer_id == 0) {
        $c_res = mysqli_query($conn, "SELECT customer_id FROM customers WHERE phone = '$phone'");
        $customer_id = mysqli_fetch_assoc($c_res)['customer_id'];
    }

    // --- ບັນທຶກການຈອງ ---
    $sql_book = "INSERT INTO bookings (customer_id, tour_id, coupon_id, travel_date, num_people, total_price, discount_amount, note, status) 
                 VALUES ($customer_id, $tour_id, $coupon_id, '$travel_date', $num_people, $total_price, $discount_amount, '$note', 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        $booking_id = mysqli_insert_id($conn);

        if (isset($_POST['participant_names'])) {
            foreach ($_POST['participant_names'] as $index => $p_name) {
                $p_name_safe = mysqli_real_escape_string($conn, $p_name);
                $p_phone_safe = mysqli_real_escape_string($conn, $_POST['participant_phones'][$index]);
                if (!empty($p_name_safe)) {
                    mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_phone) VALUES ($booking_id, '$p_name_safe', '$p_phone_safe')");
                }
            }
        }

        $default_tasks = ['ກວດເຊັກຍອດເງິນຊຳລະ', 'ຈອງໂຮງແຮມ/ທີ່ພັກ', 'ຕິດຕໍ່ລົດ ແລະ ຄົນຂັບ', 'ມອບໝາຍໄກ້ຜູ້ນຳທ່ຽວ', 'ກຽມນ້ຳດື່ມ ແລະ ອາຫານວ່າງ'];
        foreach ($default_tasks as $task_label) {
            $label_safe = mysqli_real_escape_string($conn, $task_label);
            mysqli_query($conn, "INSERT INTO booking_tasks (booking_id, task_label, is_completed) VALUES ($booking_id, '$label_safe', 0)");
        }

        header("Location: checkout.php?booking_id=$booking_id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>