<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm px-4 py-2">
    <div class="container-fluid p-0">
        <!-- ເບື້ອງຊ້າຍ (ວ່າງໄວ້ ຫຼື ໃສ່ປຸ່ມ Toggle) -->
        <div class="d-none d-sm-inline-block mr-auto">
            <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y'); ?></span>
        </div>

        <!-- ເບື້ອງຂວາ (ຂໍ້ມູນຜູ້ໃຊ້) -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end me-2 d-none d-lg-block">
                        <div class="fw-bold text-dark small"><?php echo $_SESSION['fullname']; ?></div>
                        <div class="text-muted" style="font-size: 0.7rem;"><?php echo $_SESSION['role']; ?></div>
                    </div>
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; font-weight: bold;">
                        <?php echo mb_substr($_SESSION['fullname'], 0, 1, 'UTF-8'); ?>
                    </div>
                </a>
                <!-- Dropdown Menu -->
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 animated--grow-in mt-2" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item py-2" href="#"><i class="fas fa-user fa-sm fa-fw me-2 text-muted"></i> ຂໍ້ມູນສ່ວນຕົວ</a></li>
                    <li><a class="dropdown-item py-2" href="#"><i class="fas fa-cogs fa-sm fa-fw me-2 text-muted"></i> ຕັ້ງຄ່າ</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item py-2 text-danger fw-bold" href="<?php echo BASE_URL; ?>logout.php" onclick="return confirm('ຕ້ອງການອອກຈາກລະບົບແທ້ບໍ່?')">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw me-2"></i> ອອກຈາກລະບົບ
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<style>
    .topbar { height: 70px; }
    .dropdown-item:active { background-color: #4e73df; }
    .animated--grow-in { animation: growIn 0.2s ease-out; }
    @keyframes growIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>