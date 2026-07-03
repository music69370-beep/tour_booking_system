<?php
/**
 * ໄຟລ໌ລວມຟັງຊັນຊ່ວຍເຫຼືອ (Helper Functions)
 * ຖືກ include ໂດຍ config/db.php ໃຫ້ໃຊ້ໄດ້ທຸກໜ້າ
 */

/**
 * ບັນທຶກໄຟລ໌ຮູບພາບທີ່ອັບໂຫລດເຂົ້າມາ ພ້ອມກວດສອບຄວາມປອດໄພ
 *
 * - ກວດສອບນາມສະກຸນ (whitelist) ແລະ MIME type ແທ້ຈິງຂອງໄຟລ໌
 * - ຕັ້ງຊື່ໄຟລ໌ໃໝ່ແບບສຸ່ມ ປ້ອງກັນການ overwrite ແລະ ການເດົາ path
 * - ປ້ອງກັນການອັບໂຫລດ .php ຫຼື ໄຟລ໌ອັນຕະລາຍອື່ນໆ
 *
 * @param array  $file       ຂໍ້ມູນຈາກ $_FILES['xxx']
 * @param string $target_dir ໂຟເດີປາຍທາງ (ມີ / ທ້າຍ ຫຼື ບໍ່ກໍ່ໄດ້)
 * @param string $prefix     ຄຳນຳໜ້າຊື່ໄຟລ໌ (ເຊັ່ນ "lead_", "doc_")
 * @return string|false      ຊື່ໄຟລ໌ໃໝ່ເມື່ອສຳເລັດ, "" ເມື່ອບໍ່ມີໄຟລ໌, false ເມື່ອບໍ່ຖືກຕ້ອງ
 */
function save_uploaded_image($file, $target_dir, $prefix = '')
{
    // ບໍ່ມີໄຟລ໌ສົ່ງມາ (field ວ່າງ) -> ຖືວ່າປົກກະຕິ
    if (empty($file['name']) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $allowed_ext  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $allowed_mime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // 1. ກວດສອບນາມສະກຸນ
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext, true)) {
        return false;
    }

    // 2. ກວດສອບ MIME type ແທ້ຈິງ (ບໍ່ເຊື່ອນາມສະກຸນຢ່າງດຽວ)
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed_mime, true)) {
            return false;
        }
    }

    // 3. ສ້າງໂຟເດີຖ້າຍັງບໍ່ມີ
    $target_dir = rtrim($target_dir, '/\\') . '/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // 4. ຕັ້ງຊື່ໄຟລ໌ໃໝ່ແບບສຸ່ມ (ບໍ່ໃຊ້ຊື່ເດີມຂອງຜູ້ໃຊ້)
    $safe_name = $prefix . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;

    if (move_uploaded_file($file['tmp_name'], $target_dir . $safe_name)) {
        return $safe_name;
    }

    return false;
}

/**
 * ສະແດງ string ໃນ HTML ຢ່າງປອດໄພ (ກັນ XSS)
 */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
