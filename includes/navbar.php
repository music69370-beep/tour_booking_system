<?php
// 1. ດຶງຂໍ້ມູນຜູ້ໃຊ້ໃໝ່ສຸດຈາກ Database (ເພື່ອໃຫ້ຮູບ ແລະ ຊື່ ອັບເດດຕະຫຼອດ)
$session_user_id = $_SESSION['user_id'];
$nav_user_res = mysqli_query($conn, "SELECT fullname, role, profile_pic FROM users WHERE user_id = '$session_user_id'");
$nav_user_data = mysqli_fetch_assoc($nav_user_res);

// 2. ກຳນົດເສັ້ນທາງຮູບພາບ
$nav_profile_pic = $nav_user_data['profile_pic'];
$nav_img_path = "../../assets/uploads/users/" . $nav_profile_pic;

// ກວດສອບວ່າມີໄຟລ໌ແທ້ຫຼືບໍ່
if (!empty($nav_profile_pic) && file_exists($nav_img_path)) {
    $nav_display_img = BASE_URL . "assets/uploads/users/" . $nav_profile_pic;
} else {
    // ຖ້າບໍ່ມີຮູບ ໃຫ້ໃຊ້ຮູບ Default (ກະລຸນາຫາຮູບ default-user.png ໄປໄວ້ໃນ assets/img/)
    $nav_display_img = BASE_URL . "assets/img/default-user.png";
}
?>

<nav class="navbar navbar-expand navbar-light bg-white topbar sticky-top shadow-sm px-4 py-2" style="z-index: 99; height: 70px;">
    <div class="container-fluid p-0">
        <!-- ເບື້ອງຊ້າຍ: ສະແດງວັນທີ -->
        <div class="d-none d-sm-inline-block mr-auto">
            <h5 class="mb-0 text-dark fw-bold font-lao">
                <i class="far fa-calendar-alt text-primary me-2"></i> <?php echo date('d/m/Y'); ?>
            </h5>
        </div>

        <!-- ເບື້ອງຂວາ: ຂໍ້ມູນຜູ້ໃຊ້ (Dropdown) -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle d-flex align-items-center text-decoration-none" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end me-3 d-none d-lg-block">
                        <!-- ສະແດງຊື່ເຕັມ -->
                        <div class="fw-bold text-dark small font-lao"><?php echo $nav_user_data['fullname']; ?></div>
                        <!-- ສະແດງລະດັບສິດ -->
                        <div class="text-muted small font-lao" style="font-size: 0.7rem;"><?php echo $nav_user_data['role']; ?> (ລະດັບສິດ)</div>
                    </div>
                    
                    <!-- ສະແດງຮູບໂປຣຟາຍ -->
                    <div class="shadow-sm rounded-circle border overflow-hidden bg-light d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <img src="<?php echo $nav_display_img; ?>" alt="profile" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </a>
                
                <!-- Dropdown Menu -->
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 animated--grow-in mt-2" aria-labelledby="userDropdown" style="min-width: 200px;">
                    <li>
                        <div class="px-3 py-2 d-lg-none border-bottom mb-2 bg-light">
                            <div class="fw-bold text-dark small font-lao"><?php echo $nav_user_data['fullname']; ?></div>
                            <div class="text-muted small font-lao" style="font-size: 0.7rem;"><?php echo $nav_user_data['role']; ?></div>
                        </div>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 font-lao" href="<?php echo BASE_URL; ?>pages/users/index.php">
                            <i class="fas fa-user-circle fa-sm fa-fw me-2 text-muted"></i> ໂປຣຟາຍຂອງຂ້ອຍ
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 font-lao" href="#">
                            <i class="fas fa-cogs fa-sm fa-fw me-2 text-muted"></i> ຕັ້ງຄ່າລະບົບ
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <!-- ປຸ່ມອອກຈາກລະບົບ -->
                        <a class="dropdown-item py-2 text-danger fw-bold font-lao" href="javascript:void(0)" onclick="confirmLogout()">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw me-2"></i> ອອກຈາກລະບົບ
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<style>
    /* ເອັບເຟັກ Dropdown */
    .animated--grow-in { animation: growIn 0.2s ease-out; }
    @keyframes growIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .dropdown-item:hover { background-color: #f8f9fa; color: #dc3545; }
    .dropdown-item:active { background-color: #dc3545; color: white; }
    
    /* ປັບຄວາມສູງຂອງໂຕໜັງສື */
    .line-height-1 { line-height: 1.2; }
</style>