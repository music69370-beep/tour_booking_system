<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ຮັບຂໍ້ມູນລູກຄ້າ
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // ຮັບຂໍ້ມູນການຈອງ
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']); // ຮັບວັນທີຈາກແພັກເກັດ
    $num_people = intval($_POST['num_people']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    
    // ຮັບຂໍ້ມູນສ່ວນຫຼຸດ
    $coupon_id = !empty($_POST['coupon_id']) ? intval($_POST['coupon_id']) : "NULL";
    $discount_amount = !empty($_POST['discount_val']) ? floatval($_POST['discount_val']) : 0;

    // ຄຳນວນລາຄາລວມ (ດຶງຈາກ DB ເພື່ອຄວາມປອດໄພ)
    $tour_res = mysqli_query($conn, "SELECT price FROM tours WHERE tour_id = '$tour_id'");
    $tour_data = mysqli_fetch_assoc($tour_res);
    $price_per_person = $tour_data['price'];
    
    $total_price = ($price_per_person * $num_people) - $discount_amount;
    if ($total_price < 0) $total_price = 0;

    // 1. ບັນທຶກ ຫຼື ອັບເດດຂໍ້ມູນລູກຄ້າ
    mysqli_query($conn, "INSERT INTO customers (fullname, phone, email) VALUES ('$fullname', '$phone', '$email') ON DUPLICATE KEY UPDATE fullname='$fullname', email='$email'");
    $customer_id = mysqli_insert_id($conn);
    if ($customer_id == 0) {
        $c_res = mysqli_query($conn, "SELECT customer_id FROM customers WHERE phone = '$phone'");
        $customer_id = mysqli_fetch_assoc($c_res)['customer_id'];
    }

    // 2. ບັນທຶກການຈອງ (ໃຊ້ຄ່າ $coupon_id ໂດຍກົງ ບໍ່ຕ້ອງໃສ່ ' ')
    $sql_book = "INSERT INTO bookings (customer_id, tour_id, coupon_id, travel_date, num_people, total_price, discount_amount, note, status) 
                 VALUES ($customer_id, $tour_id, $coupon_id, '$travel_date', $num_people, $total_price, $discount_amount, '$note', 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        $booking_id = mysqli_insert_id($conn);

        // 3. ບັນທຶກລາຍຊື່ຜູ້ຮ່ວມທາງ
        if (isset($_POST['participant_names'])) {
            $p_names = $_POST['participant_names'];
            $p_phones = $_POST['participant_phones'];
            foreach ($p_names as $index => $p_name) {
                $name = mysqli_real_escape_string($conn, $p_name);
                $p_phone = mysqli_real_escape_string($conn, $p_phones[$index]);
                if (!empty($name)) {
                    mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_phone) VALUES ($booking_id, '$name', '$p_phone')");
                }
            }
        }

        // 4. ສ້າງ Checklist ວຽກ
        $tasks = ['ກວດເຊັກຍອດເງິນ', 'ຈອງທີ່ພັກ', 'ຕິດຕໍ່ລົດ', 'ມອບໝາຍໄກ້', 'ກຽມນ້ຳດື່ມ'];
        foreach ($tasks as $t) mysqli_query($conn, "INSERT INTO booking_tasks (booking_id, task_label) VALUES ($booking_id, '$t')");

        header("Location: checkout.php?booking_id=$booking_id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>