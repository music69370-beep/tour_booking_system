<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $num_people = intval($_POST['num_people']);
    $total_price = mysqli_real_escape_string($conn, $_POST['total_price']);

    // 1. ເພີ່ມຂໍ້ມູນລູກຄ້າໃໝ່
    $sql_cust = "INSERT INTO customers (fullname, phone) VALUES ('$fullname', '$phone')";
    mysqli_query($conn, $sql_cust);
    $customer_id = mysqli_insert_id($conn);

    // 2. ບັນທຶກການຈອງ (ສະຖານະເລີ່ມຕົ້ນເປັນ Pending)
    $sql_book = "INSERT INTO bookings (customer_id, tour_id, num_people, total_price, status) 
                 VALUES ($customer_id, $tour_id, $num_people, $total_price, 'Pending')";

    if (mysqli_query($conn, $sql_book)) {
        // ສົ່ງ Pop-up ແຈ້ງເຕືອນດ້ວຍ SweetAlert2
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
                    title: 'ການຈອງຂອງທ່ານສຳເລັດແລ້ວ!',
                    text: 'ກະລຸນາລໍຖ້າການຕິດຕໍ່ກັບຈາກເຈົ້າໜ້າທີ່ພວກເຮົາ',
                    confirmButtonText: 'ຕົກລົງ'
                }).then(() => {
                    window.location.href = 'index.php';
                });
            </script>
        </body>
        </html>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>