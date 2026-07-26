<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. ກວດສອບກ່ອນວ່າມີວຽກຢູ່ແລ້ວບໍ່
    $check = mysqli_query($conn, "SELECT * FROM booking_tasks WHERE booking_id = '$id'");
    
    if (mysqli_num_rows($check) == 0) {
        // 2. ລາຍການວຽກມາດຕະຖານ
        $default_tasks = [
            'ກວດເຊັກຍອດເງິນຊຳລະ',
            'ຈອງໂຮງແຮມ/ທີ່ພັກ',
            'ຕິດຕໍ່ລົດ ແລະ ຄົນຂັບ',
            'ມອບໝາຍໄກ້ຜູ້ນຳທ່ຽວ',
            'ກຽມນ້ຳດື່ມ ແລະ ອາຫານວ່າງ'
        ];

        foreach ($default_tasks as $task) {
            $task_safe = mysqli_real_escape_string($conn, $task);
            $sql = "INSERT INTO booking_tasks (booking_id, task_label, is_completed) VALUES ('$id', '$task_safe', 0)";
            
            if (!mysqli_query($conn, $sql)) {
                // ຖ້າ Insert ບໍ່ໄດ້ ໃຫ້ຢຸດ ແລະ ໂຊ Error ເພື່ອຫາສາເຫດ
                die("ເກີດຂໍ້ຜິດພາດໃນການສ້າງວຽກ: " . mysqli_error($conn));
            }
        }
    }
    
    header("Location: view.php?id=$id&msg=success#task-section");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>