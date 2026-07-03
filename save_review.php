<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_id = intval($_POST['tour_id']);
    $customer_id = intval($_POST['customer_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    if ($rating < 1) { $rating = 1; }
    if ($rating > 5) { $rating = 5; }

    // ບັນທຶກລົງ Table reviews (prepared statement)
    $stmt = mysqli_prepare($conn, "INSERT INTO reviews (tour_id, customer_id, rating, comment, status) VALUES (?, ?, ?, ?, 'Approved')");
    mysqli_stmt_bind_param($stmt, "iiis", $tour_id, $customer_id, $rating, $comment);

    if (mysqli_stmt_execute($stmt)) {
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