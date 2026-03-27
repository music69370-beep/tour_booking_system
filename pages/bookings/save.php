<?php
include '../../config/db.php';

if (isset($_POST['save_booking'])) {
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $num_people = intval($_POST['num_people']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    $tour_res = mysqli_query($conn, "SELECT price FROM tours WHERE tour_id = '$tour_id'");
    $tour_data = mysqli_fetch_assoc($tour_res);
    $total_price = $tour_data['price'] * $num_people;

    $sql_book = "INSERT INTO bookings (customer_id, tour_id, travel_date, num_people, total_price, status) 
                 VALUES ($customer_id, $tour_id, '$travel_date', $num_people, $total_price, 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        $booking_id = mysqli_insert_id($conn);

        // 1. ບັນທຶກຜູ້ຮ່ວມທາງ (ຖ້າມີ)
        if (isset($_POST['participant_names'])) {
            foreach ($_POST['participant_names'] as $index => $p_name) {
                $name = mysqli_real_escape_string($conn, $p_name);
                $p_phone = mysqli_real_escape_string($conn, $_POST['participant_phones'][$index]);
                mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_phone) VALUES ($booking_id, '$name', '$p_phone')");
            }
        }

        // 2. *** ເພີ່ມວຽກ Checklist ພື້ນຖານອັດຕະໂນມັດ ***
        $default_tasks = [
            'ກວດເຊັກຍອດເງິນຊຳລະ',
            'ຈອງໂຮງແຮມ/ທີ່ພັກ',
            'ຕິດຕໍ່ລົດ ແລະ ຄົນຂັບ',
            'ມອບໝາຍໄກ້ຜູ້ນຳທ່ຽວ',
            'ກຽມນ້ຳດື່ມ ແລະ ອາຫານວ່າງ'
        ];
        foreach ($default_tasks as $task) {
            mysqli_query($conn, "INSERT INTO booking_tasks (booking_id, task_label) VALUES ($booking_id, '$task')");
        }

        header("Location: index.php?msg=success");
        exit();
    }
    // --- ວາງ Code ນີ້ ຫຼັງຈາກ INSERT bookings ສຳເລັດ ---
    if (mysqli_query($conn, $sql_book)) {
        $booking_id = mysqli_insert_id($conn);

        // ສ້າງລາຍການວຽກເລີ່ມຕົ້ນ 5 ຢ່າງ
        $default_tasks = [
            'ກວດເຊັກຍອດເງິນຊຳລະ',
            'ຈອງໂຮງແຮມ/ທີ່ພັກ',
            'ຕິດຕໍ່ລົດ ແລະ ຄົນຂັບ',
            'ມອບໝາຍໄກ້ຜູ້ນຳທ່ຽວ',
            'ກຽມນ້ຳດື່ມ ແລະ ອາຫານວ່າງ'
        ];

        foreach ($default_tasks as $task_name) {
            $task_name_safe = mysqli_real_escape_string($conn, $task_name);
            mysqli_query($conn, "INSERT INTO booking_tasks (booking_id, task_label, is_completed) VALUES ($booking_id, '$task_name_safe', 0)");
        }

        header("Location: index.php?msg=success");
        exit();
    }
}
?>