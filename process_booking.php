<?php
// 1. ເປີດການສະແດງ Error ເພື່ອໃຫ້ຮູ້ບັນຫາແທ້ຈິງ (ຖ້າແກ້ແລ້ວຄ່ອຍປິດ)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config/db.php';
/** @var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // --- 1. ຮັບຂໍ້ມູນລູກຄ້າຫຼັກ ---
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $birthday = mysqli_real_escape_string($conn, $_POST['birthday']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
    $id_card_no = mysqli_real_escape_string($conn, $_POST['id_card_no']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $emergency_name = mysqli_real_escape_string($conn, $_POST['emergency_name']);
    $emergency_phone = mysqli_real_escape_string($conn, $_POST['emergency_phone']);
    
    // ຈັດການຮູບພາບບັດ
    $id_card_image = "";
    if (!empty($_FILES['id_card_image']['name'])) {
        $id_card_image = time() . "_lead_" . $_FILES['id_card_image']['name'];
        if (!is_dir("assets/uploads/customers/")) {
            mkdir("assets/uploads/customers/", 0777, true);
        }
        move_uploaded_file($_FILES['id_card_image']['tmp_name'], "assets/uploads/customers/" . $id_card_image);
    }

    // ບັນທຶກ ຫຼື ອັບເດດຂໍ້ມູນລູກຄ້າ
    $sql_cust = "INSERT INTO customers (fullname, gender, birthday, nationality, id_card_no, id_card_image, phone, email, address, emergency_name, emergency_phone) 
                 VALUES ('$fullname', '$gender', '$birthday', '$nationality', '$id_card_no', '$id_card_image', '$phone', '$email', '$address', '$emergency_name', '$emergency_phone')
                 ON DUPLICATE KEY UPDATE 
                 fullname='$fullname', gender='$gender', birthday='$birthday', nationality='$nationality', id_card_no='$id_card_no', 
                 email='$email', address='$address', emergency_name='$emergency_name', emergency_phone='$emergency_phone'";
    
    if (!mysqli_query($conn, $sql_cust)) {
        die("Error Saving Customer: " . mysqli_error($conn));
    }
    
    // ດຶງ Customer ID
    $customer_id = mysqli_insert_id($conn);
    if ($customer_id == 0) {
        $res = mysqli_query($conn, "SELECT customer_id FROM customers WHERE phone='$phone'");
        $row = mysqli_fetch_assoc($res);
        $customer_id = $row['customer_id'];
    }

    // --- 2. ຮັບຂໍ້ມູນການຈອງ ແລະ ຄິດໄລ່ລາຄາ ---
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $num_people = intval($_POST['num_people']);
    $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
    $selected_seats = mysqli_real_escape_string($conn, $_POST['selected_seats']);
    
    // ດຶງຂໍ້ມູນທົວເພື່ອເອົາລາຄາ ແລະ ວັນທີ
    $tour_res = mysqli_query($conn, "SELECT price, start_date FROM tours WHERE tour_id = '$tour_id'");
    $tour_data = mysqli_fetch_assoc($tour_res);
    if (!$tour_data) {
        die("Error: Tour not found.");
    }

    $supplement_fee = ($room_type == 'Single') ? 200000 : 0;
    $total_price = ($tour_data['price'] * $num_people) + $supplement_fee;
    $travel_date = $tour_data['start_date'];

    // ບັນທຶກການຈອງ (Bookings)
    $sql_book = "INSERT INTO bookings (customer_id, tour_id, travel_date, num_people, room_type, total_price, single_supplement_fee, selected_seats, status) 
                 VALUES ('$customer_id', '$tour_id', '$travel_date', '$num_people', '$room_type', '$total_price', '$supplement_fee', '$selected_seats', 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        $booking_id = mysqli_insert_id($conn);

        // --- 3. ບັນທຶກຜູ້ຮ່ວມທາງ (ຖ້າມີ) ---
        if (isset($_POST['participant_names']) && is_array($_POST['participant_names'])) {
            $p_names = $_POST['participant_names'];
            $p_id_cards = $_POST['participant_id_cards'];
            foreach ($p_names as $index => $p_name) {
                $name = mysqli_real_escape_string($conn, $p_name);
                $id_card = mysqli_real_escape_string($conn, $p_id_cards[$index]);
                if (!empty($name)) {
                    $note = "Traveling with " . $fullname;
                    mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_id_card, participant_phone) 
                                         VALUES ('$booking_id', '$name', '$id_card', '$note')");
                }
            }
        }

        // ໄປໜ້າ Checkout
        header("Location: checkout.php?booking_id=$booking_id");
        exit();
    } else {
        // ຖ້າມັນຂຶ້ນ Error ຢູ່ນີ້ ໝາຍຄວາມວ່າຖານຂໍ້ມູນເຈົ້າຍັງບໍ່ມີ Column ໃໝ່
        die("Error Saving Booking: " . mysqli_error($conn) . "<br>SQL: " . $sql_book);
    }
}
?>