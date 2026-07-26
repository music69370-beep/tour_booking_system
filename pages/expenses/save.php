<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['btn_save'])) {
    
    // ດຶງ ID ຂອງພະນັກງານຈາກ Session
    $user_id = $_SESSION['user_id']; 

    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $desc = mysqli_real_escape_string($conn, $_POST['note']);

    // ເພີ່ມ created_by ເຂົ້າໃນຄຳສັ່ງ INSERT
    $sql = "INSERT INTO tour_expenses (tour_id, travel_date, category, amount, description, created_by) 
            VALUES ('$tour_id', '$travel_date', '$category', '$amount', '$desc', '$user_id')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
    exit();
}
?>