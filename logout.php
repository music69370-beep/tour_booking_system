<?php
session_start(); // ເຂົ້າຫາ Session ປັດຈຸບັນ
session_unset(); // ລ້າງຕົວແປ Session ທັງໝົດ
session_destroy(); // ທຳລາຍ Session ຖິ້ມ

// ສົ່ງກັບໄປໜ້າ Login ພ້ອມແຈ້ງເຕືອນ
header("Location: login.php?msg=logout");
exit();
?>