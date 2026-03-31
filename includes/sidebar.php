<?php
// ກວດສອບ Path ປັດຈຸບັນເພື່ອເຮັດສີຄ້າງ (Active State)
$current_page = $_SERVER['PHP_SELF'];
?>

<nav class="col-md-2 d-none d-md-block sidebar shadow-sm p-0 bg-white">
    <div class="position-sticky">
        <!-- Logo Section -->
        <div class="p-3 text-center border-bottom bg-primary text-white shadow-sm">
            <h4 class="fw-bold mb-0"><i class="fas fa-plane-departure me-2"></i>ລະບົບບໍລິຫານ ຈອງທົວ</h4>
        </div>
        
        <div class="sidebar-content p-2">
            
            <!-- Category: MENU -->
            <h6 class="sidebar-heading px-3 mt-3 mb-2 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">ເມນູຫຼັກ</h6>
            <ul class="nav flex-column mb-3">
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'dashboard/index.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" 
                       href="<?php echo BASE_URL; ?>pages/dashboard/index.php">
                        <i class="fas fa-th-large me-2 <?php echo (strpos($current_page, 'dashboard/index.php') !== false) ? 'text-white' : 'text-primary'; ?>"></i> Dashboard
                    </a>
                </li>
            </ul>

            <!-- Category: ENTRY FORMS -->
            <h6 class="sidebar-heading px-3 mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">ການບັນທຶກຂໍ້ມູນ</h6>
            <ul class="nav flex-column mb-3">
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'bookings/add.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/bookings/add.php">
                        <i class="fas fa-plus-circle me-2 <?php echo (strpos($current_page, 'bookings/add.php') !== false) ? 'text-white' : 'text-success'; ?>"></i> ຈອງທົວໃໝ່
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'tours/add.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/tours/add.php">
                        <i class="fas fa-folder-plus me-2 <?php echo (strpos($current_page, 'tours/add.php') !== false) ? 'text-white' : 'text-info'; ?>"></i> ເພີ່ມແພັກເກັດທົວ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'vehicles/add.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/vehicles/add.php">
                        <i class="fas fa-bus-alt me-2 <?php echo (strpos($current_page, 'vehicles/add.php') !== false) ? 'text-white' : 'text-warning'; ?>"></i> ເພີ່ມລົດ/ຄົນຂັບ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'guides/add.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/guides/add.php">
                        <i class="fas fa-user-plus me-2 <?php echo (strpos($current_page, 'guides/add.php') !== false) ? 'text-white' : 'text-primary'; ?>"></i> ເພີ່ມໄກ້ຜູ້ນຳທ່ຽວ
                    </a>
                </li>
                <?php if(isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'coupons/add.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/coupons/add.php">
                        <i class="fas fa-ticket-alt me-2 <?php echo (strpos($current_page, 'coupons/add.php') !== false) ? 'text-white' : 'text-danger'; ?>"></i> ສ້າງຄູປອງໃໝ່
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'customers/add.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/customers/add.php">
                        <i class="fas fa-user-tag me-2 <?php echo (strpos($current_page, 'customers/add.php') !== false) ? 'text-white' : 'text-secondary'; ?>"></i> ເພີ່ມຂໍ້ມູນລູກຄ້າ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'payments/add.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/payments/add.php">
                        <i class="fas fa-cash-register me-2 <?php echo (strpos($current_page, 'payments/add.php') !== false) ? 'text-white' : 'text-danger'; ?>"></i> ບັນທຶກຮັບເງິນ
                    </a>
                </li>
            </ul>

            <!-- Category: REPORTS -->
            <h6 class="sidebar-heading px-3 mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">ລາຍງານ ແລະ ຈັດການ</h6>
            <ul class="nav flex-column mb-3">
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'bookings/calendar.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/bookings/calendar.php">
                        <i class="fas fa-calendar-alt me-2 <?php echo (strpos($current_page, 'bookings/calendar.php') !== false) ? 'text-white' : 'text-primary'; ?>"></i> ປະຕິທິນການຈອງ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'bookings/index.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/bookings/index.php">
                        <i class="fas fa-list-ul me-2 <?php echo (strpos($current_page, 'bookings/index.php') !== false) ? 'text-white' : 'text-primary'; ?>"></i> ລາຍການຈອງທັງໝົດ
                    </a>
                </li>
                
                <?php if(isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'payments/index.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/payments/index.php">
                        <i class="fas fa-file-invoice-dollar me-2 <?php echo (strpos($current_page, 'payments/index.php') !== false) ? 'text-white' : 'text-success'; ?>"></i> ປະຫວັດການຮັບເງິນ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'coupons/index.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/coupons/index.php">
                        <i class="fas fa-tags me-2 <?php echo (strpos($current_page, 'coupons/index.php') !== false) ? 'text-white' : 'text-danger'; ?>"></i> ລາຍການຄູປອງ
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'tours/index.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/tours/index.php">
                        <i class="fas fa-map-marked-alt me-2 <?php echo (strpos($current_page, 'tours/index.php') !== false) ? 'text-white' : 'text-info'; ?>"></i> ລາຍການທົວທັງໝົດ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'vehicles/index.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/vehicles/index.php">
                        <i class="fas fa-shuttle-van me-2 <?php echo (strpos($current_page, 'vehicles/index.php') !== false) ? 'text-white' : 'text-warning'; ?>"></i> ລາຍການລົດທັງໝົດ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'guides/index.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/guides/index.php">
                        <i class="fas fa-user-tie me-2 <?php echo (strpos($current_page, 'guides/index.php') !== false) ? 'text-white' : 'text-primary'; ?>"></i> ລາຍການໄກ້ທັງໝົດ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'customers/index.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/customers/index.php">
                        <i class="fas fa-users me-2 <?php echo (strpos($current_page, 'customers/index.php') !== false) ? 'text-white' : 'text-success'; ?>"></i> ລາຍຊື່ລູກຄ້າ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'reviews/index.php') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" href="<?php echo BASE_URL; ?>pages/reviews/index.php">
                        <i class="fas fa-comment-dots me-2 <?php echo (strpos($current_page, 'reviews/index.php') !== false) ? 'text-white' : 'text-warning'; ?>"></i> ຈັດການຄຳຍ້ອງຍໍ
                    </a>
                </li>
            </ul>

            <!-- Category: SYSTEM -->
            <?php if(isAdmin()): ?>
            <h6 class="sidebar-heading px-3 mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">ຕັ້ງຄ່າລະບົບ</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link py-2 rounded <?php echo (strpos($current_page, 'users/') !== false) ? 'active bg-primary text-white shadow' : 'text-dark'; ?>" 
                       href="<?php echo BASE_URL; ?>pages/users/index.php">
                        <i class="fas fa-users-cog me-2 <?php echo (strpos($current_page, 'users/') !== false) ? 'text-white' : 'text-danger'; ?>"></i> ຈັດການຜູ້ໃຊ້
                    </a>
                </li>
            </ul>
            <?php endif; ?>

            <hr class="mx-3 mt-4">
            <li class="nav-item mb-5 p-2">
                <a class="nav-link text-danger py-2 rounded fw-bold" href="javascript:void(0)" onclick="confirmLogout()">
                    <i class="fas fa-sign-out-alt me-2"></i> ອອກຈາກລະບົບ
                </a>
            </li>
        </div>
    </div>
</nav>

<style>
    .sidebar { height: 100vh; overflow-y: auto; border-right: 1px solid #e3e6f0; position: fixed; left: 0; top: 0; z-index: 100; }
    .nav-link { font-size: 0.85rem; transition: all 0.2s ease-in-out; padding: 10px 15px; margin: 2px 8px; border-radius: 10px !important; }
    .nav-link:hover:not(.active) { background-color: #f8f9fc; color: #4e73df !important; transform: translateX(5px); }
    .nav-link.active { box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2) !important; }
    .nav-link i { width: 25px; text-align: center; }
    .sidebar::-webkit-scrollbar { width: 5px; }
    .sidebar::-webkit-scrollbar-thumb { background: #eee; border-radius: 10px; }
</style>