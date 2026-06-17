<?php
include 'config/db.php';
/** @var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. ຮັບຂໍ້ມູນລູກຄ້າຫຼັກຄົບທຸກ Field
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
    $id_card_no = mysqli_real_escape_string($conn, $_POST['id_card_no']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $emergency_name = mysqli_real_escape_string($conn, $_POST['emergency_name']);
    $emergency_phone = mysqli_real_escape_string($conn, $_POST['emergency_phone']);
    
    // 2. ຈັດການຮູບພາບບັດ (ຖ້າມີ)
    $id_card_image = "";
    if (!empty($_FILES['id_card_image']['name'])) {
        $id_card_image = time() . "_lead_" . $_FILES['id_card_image']['name'];
        move_uploaded_file($_FILES['id_card_image']['tmp_name'], "assets/uploads/customers/" . $id_card_image);
    }

    // 3. ບັນທຶກ/ອັບເດດ ລູກຄ້າເຂົ້າ Customers table
    $sql_cust = "INSERT INTO customers (fullname, gender, birthday, nationality, id_card_no, id_card_image, phone, email, address, emergency_name, emergency_phone) 
                 VALUES ('$fullname', '$gender', '$birthday', '$nationality', '$id_card_no', '$id_card_image', '$phone', '$email', '$address', '$emergency_name', '$emergency_phone')
                 ON DUPLICATE KEY UPDATE 
                 fullname='$fullname', gender='$gender', birthday='$birthday', nationality='$nationality', id_card_no='$id_card_no', 
                 email='$email', address='$address', emergency_name='$emergency_name', emergency_phone='$emergency_phone'";
    
    mysqli_query($conn, $sql_cust);
    $customer_id = mysqli_insert_id($conn) ?: mysqli_fetch_assoc(mysqli_query($conn, "SELECT customer_id FROM customers WHERE phone='$phone'"))['customer_id'];

    // 4. ບັນທຶກການຈອງ (Bookings)
    $tour_id = $_POST['tour_id'];
    $travel_date = $_POST['travel_date'];
    $num_people = intval($_POST['num_people']);
    $selected_seats = mysqli_real_escape_string($conn, $_POST['selected_seats']);
    $total_price = floatval($_POST['price']) * $num_people;

    $sql_book = "INSERT INTO bookings (customer_id, tour_id, travel_date, num_people, selected_seats, total_price, status) 
                 VALUES ($customer_id, $tour_id, '$travel_date', $num_people, '$selected_seats', $total_price, 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        $booking_id = mysqli_insert_id($conn);

        // 5. ບັນທຶກຜູ້ຮ່ວມທາງ (ຄົນທີ 2 ເປັນຕົ້ນໄປ)
        if (isset($_POST['participant_names'])) {
            $p_names = $_POST['participant_names'];
            $p_id_cards = $_POST['participant_id_cards'];
            foreach ($p_names as $index => $p_name) {
                $name = mysqli_real_escape_string($conn, $p_name);
                $id_card = mysqli_real_escape_string($conn, $p_id_cards[$index]);
                if (!empty($name)) {
                    $note = "Traveling with " . $fullname;
                    mysqli_query($conn, "INSERT INTO booking_participants (booking_id, participant_name, participant_id_card, participant_phone) 
                                         VALUES ($booking_id, '$name', '$id_card', '$note')");
                }
            }
        }
        header("Location: checkout.php?booking_id=$booking_id");
        exit();
    }
}
?>