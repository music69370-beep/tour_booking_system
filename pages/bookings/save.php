<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_booking'])) {
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $num_people = intval($_POST['num_people']);
    $selected_seats = mysqli_real_escape_string($conn, $_POST['selected_seats']); // ຮັບຄ່າເລກບ່ອນນັ່ງ
    $total_price = mysqli_real_escape_string($conn, $_POST['total_price']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    // ເພີ່ມ selected_seats ເຂົ້າໄປໃນ INSERT
    $sql = "INSERT INTO bookings (customer_id, tour_id, travel_date, num_people, selected_seats, total_price, note, status) 
            VALUES ('$customer_id', '$tour_id', '$travel_date', $num_people, '$selected_seats', '$total_price', '$note', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        $booking_id = mysqli_insert_id($conn);

        // ບັນທຶກລາຍຊື່ຜູ້ຮ່ວມທາງ (ຄົນທີ 2 ເປັນຕົ້ນໄປ)
        // ໃນ save.php ບ່ອນ loop ບັນທຶກຜູ້ຮ່ວມທາງ
            if (isset($_POST['participant_names'])) {
                $names = $_POST['participant_names'];
                $phones = $_POST['participant_phones'];
                $p_seats = $_POST['participant_seats']; // ຮັບຄ່າເລກບ່ອນນັ່ງຂອງແຕ່ລະຄົນ
                
                for ($i = 0; $i < count($names); $i++) {
                    $name = mysqli_real_escape_string($conn, $names[$i]);
                    $phone = mysqli_real_escape_string($conn, $phones[$i]);
                    $seat = mysqli_real_escape_string($conn, $p_seats[$i]);
                    
                    if (!empty($name)) {
                        mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_phone, participant_seat) 
                                            VALUES ($booking_id, '$name', '$phone', '$seat')");
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
        die("Error Database: " . mysqli_error($conn));
    }
}
?>