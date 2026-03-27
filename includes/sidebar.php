<nav class="col-md-2 d-none d-md-block sidebar shadow-sm p-0 bg-white">
    <div class="position-sticky">
        <!-- Logo Section -->
        <div class="p-3 text-center border-bottom bg-primary text-white">
            <h4 class="fw-bold mb-0"><i class="fas fa-plane-departure me-2"></i>ລະບົບບໍລິຫານ ຈອງທົວ</h4>
        </div>
        
        <div class="sidebar-content p-2">
            <!-- Category: MENU -->
            <h6 class="sidebar-heading px-3 mt-3 mb-2 text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">
                ເມນູຫຼັກ (Menu)
            </h6>
            <ul class="nav flex-column mb-3">
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/dashboard/index.php">
                        <i class="fas fa-th-large me-2 text-primary"></i> Dashboard
                    </a>
                </li>
            </ul>

            <!-- Category: ENTRY FORMS -->
            <h6 class="sidebar-heading px-3 mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">
                ການບັນທຶກຂໍ້ມູນ (Entry Forms)
            </h6>
            <ul class="nav flex-column mb-3">
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/bookings/add.php">
                        <i class="fas fa-plus-circle me-2 text-success"></i> ຈອງທົວໃໝ່
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/tours/add.php">
                        <i class="fas fa-folder-plus me-2 text-info"></i> ເພີ່ມແພັກເກັດທົວ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/vehicles/add.php">
                        <i class="fas fa-bus-alt me-2 text-warning"></i> ເພີ່ມລົດ/ຄົນຂັບ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/guides/add.php">
                        <i class="fas fa-user-tie me-2 text-primary"></i> ເພີ່ມໄກ້ຜູ້ນຳທ່ຽວ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/customers/add.php">
                        <i class="fas fa-user-plus me-2 text-secondary"></i> ເພີ່ມຂໍ້ມູນລູກຄ້າ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/payments/add.php">
                        <i class="fas fa-cash-register me-2 text-danger"></i> ບັນທຶກຮັບເງິນ
                    </a>
                </li>
            </ul>

            <!-- Category: REPORTS -->
            <h6 class="sidebar-heading px-3 mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">
                ລາຍງານ ແລະ ຈັດການ (Reports)
            </h6>
            <ul class="nav flex-column mb-3">
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/bookings/index.php">
                        <i class="fas fa-list-ul me-2"></i> ລາຍການຈອງທັງໝົດ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/payments/index.php">
                        <i class="fas fa-file-invoice-dollar me-2"></i> ປະຫວັດການຮັບເງິນ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/tours/index.php">
                        <i class="fas fa-map-marked-alt me-2"></i> ລາຍການທົວທັງໝົດ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/vehicles/index.php">
                        <i class="fas fa-shuttle-van me-2"></i> ລາຍການລົດທັງໝົດ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/guides/index.php">
                        <i class="fas fa-id-badge me-2"></i> ລາຍການໄກ້ທັງໝົດ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/customers/index.php">
                        <i class="fas fa-users me-2"></i> ລາຍຊື່ລູກຄ້າ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/bookings/calendar.php">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i> ປະຕິທິນການຈອງ
                    </a>
                </li>
            </ul>

            <!-- Category: SYSTEM -->
            <h6 class="sidebar-heading px-3 mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">
                ຕັ້ງຄ່າລະບົບ (System)
            </h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-dark py-2 rounded" href="<?php echo BASE_URL; ?>pages/users/index.php">
                        <i class="fas fa-users-cog me-2"></i> ຈັດການຜູ້ໃຊ້
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .sidebar { height: 100vh; overflow-y: auto; border-right: 1px solid #e3e6f0; position: fixed; left: 0; top: 0; z-index: 100; }
    .nav-link { font-size: 0.85rem; transition: all 0.2s; padding: 10px 15px; margin: 2px 8px; border-radius: 8px !important; }
    .nav-link:hover { background-color: #f8f9fc; color: #4e73df !important; transform: translateX(5px); }
    .nav-link i { width: 25px; }
</style>