<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_POST['update_tour'])) {
    $id = $_POST['tour_id'];
    $tour_code = mysqli_real_escape_string($conn, $_POST['tour_code']);
    $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = $_POST['price'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $max_seats = intval($_POST['max_seats']);
    $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']);
    
    // ຮັບຄ່າໄກ້
    $guide_id = !empty($_POST['guide_id']) ? intval($_POST['guide_id']) : "NULL";
    $old_guide_id = $_POST['old_guide_id'];

    $old_image = $_POST['old_image'];

    // ຈັດການຮູບພາບ
    $new_name = $old_image;
    if (!empty($_FILES['image']['name'])) {
        $new_name = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../../assets/uploads/tours/" . $new_name);
        if ($old_image && file_exists("../../assets/uploads/tours/" . $old_image)) {
            unlink("../../assets/uploads/tours/" . $old_image);
        }
    }

    // 1. ອັບເດດຂໍ້ມູນທົວ (ເພີ່ມ guide_id)
    $sql = "UPDATE tours SET 
            tour_code = '$tour_code', 
            tour_name = '$tour_name', 
            guide_id = $guide_id,
            category = '$category',
            price = '$price', 
            start_date = '$start_date', 
            end_date = '$end_date', 
            max_seats = '$max_seats', 
            itinerary = '$itinerary', 
            image = '$new_name'
            WHERE tour_id = $id";

    // ຕື່ມໃສ່ພາຍໃນ if (mysqli_query($conn, $sql)) ໃນ update.php

        if (mysqli_query($conn, $sql)) {
            // ... logic ອື່ນໆ ...

            // ຈັດການອັບໂຫລດຮູບ Gallery ເພີ່ມເຕີມ (ຖ້າມີການເລືອກໃໝ່)
            if (!empty($_FILES['gallery']['name'][0])) {
                foreach ($_FILES['gallery']['tmp_name'] as $k => $tmp) {
                    if(!empty($tmp)) {
                        $g_name = time() . "_gal_" . $k . "_" . basename($_FILES['gallery']['name'][$k]);
                        if (move_uploaded_file($tmp, "../../assets/uploads/tours/" . $g_name)) {
                            mysqli_query($conn, "INSERT INTO tour_images (tour_id, image_name) VALUES ($id, '$g_name')");
                        }
                    }
                }
            }

            header("Location: index.php?msg=updated");
            exit();
        } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>