<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['save_expense'])) {
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "INSERT INTO tour_expenses (tour_id, travel_date, category, amount, description) 
            VALUES ('$tour_id', '$travel_date', '$category', '$amount', '$desc')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>