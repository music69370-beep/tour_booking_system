<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_booking'])) {
    $customer_id = $_POST['customer_id'];
    $tour_id = $_POST['tour_id'];
    $travel_date = $_POST['travel_date'];
    $num_people = intval($_POST['num_people']);
    $total_price = $_POST['total_price'];
    $coupon_id = !empty($_POST['coupon_id']) ? $_POST['coupon_id'] : "NULL";
    $discount_amount = !empty($_POST['discount_amount']) ? $_POST['discount_amount'] : 0;
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    // ບັນທຶກລົງຕາຕະລາງ bookings
    $sql = "INSERT INTO bookings (customer_id, tour_id, coupon_id, travel_date, num_people, total_price, discount_amount, note, status) 
            VALUES ($customer_id, $tour_id, $coupon_id, '$travel_date', $num_people, '$total_price', '$discount_amount', '$note', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        $booking_id = mysqli_insert_id($conn);

        // ບັນທຶກຜູ້ຮ່ວມທາງ
        if (isset($_POST['participant_names'])) {
            foreach ($_POST['participant_names'] as $index => $p_name) {
                $name = mysqli_real_escape_string($conn, $p_name);
                $p_phone = mysqli_real_escape_string($conn, $_POST['participant_phones'][$index]);
                if(!empty($name)) {
                    mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_phone) VALUES ($booking_id, '$name', '$p_phone')");
                }
            }
        }

        // ສ້າງ Checklist ວຽກ
        $tasks = ['ກວດເຊັກຍອດເງິນ', 'ຈອງທີ່ພັກ', 'ຕິດຕໍ່ລົດ', 'ມອບໝາຍໄກ້', 'ກຽມນ້ຳດື່ມ'];
        foreach ($tasks as $t) {
            mysqli_query($conn, "INSERT INTO booking_tasks (booking_id, task_label) VALUES ($booking_id, '$t')");
        }

        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>