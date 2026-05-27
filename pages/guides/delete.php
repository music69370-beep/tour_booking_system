<?php
include '../../config/db.php';
/** @var mysqli $conn */

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. ດຶງຊື່ໄຟລ໌ມາລຶບ
    $res = mysqli_query($conn, "SELECT image, doc_attachment FROM guides WHERE guide_id = '$id'");
    $data = mysqli_fetch_assoc($res);

    if ($data) {
        $path = "../../assets/uploads/guides/";
        if ($data['image'] && file_exists($path . $data['image'])) unlink($path . $data['image']);
        if ($data['doc_attachment'] && file_exists($path . $data['doc_attachment'])) unlink($path . $data['doc_attachment']);

        // 2. ລຶບຈາກ DB
        if (mysqli_query($conn, "DELETE FROM guides WHERE guide_id = '$id'")) {
            header("Location: index.php?msg=deleted");
            exit();
        }
    }
}
header("Location: index.php");
?>