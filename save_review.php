<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    // ບັນທຶກລົງ Table reviews
    $sql = "INSERT INTO reviews (tour_id, customer_id, rating, comment, status) 
            VALUES ('$tour_id', '$customer_id', '$rating', '$comment', 'Approved')";

    if (mysqli_query($conn, $sql)) {
        echo "
        <!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <style>@import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap'); body { font-family: 'Noto Sans Lao', sans-serif; }</style>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'ຂອບໃຈສຳລັບຄຳຍ້ອງຍໍ!',
                    text: 'ພວກເຮົາຈະນຳເອົາຄຳຄິດເຫັນຂອງທ່ານໄປພັດທະນາໃຫ້ດີຂຶ້ນ',
                    confirmButtonText: 'ຕົກລົງ'
                }).then(() => {
                    window.history.back();
                });
            </script>
        </body>
        </html>";
    }
}
?>