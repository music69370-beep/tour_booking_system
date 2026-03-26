<nav class="col-md-2 d-none d-md-block sidebar shadow-sm p-3">
    <h4 class="text-primary fw-bold mb-4"><i class="fas fa-plane"></i> Tour Booking</h4>
    <ul class="nav flex-column gap-2">
        <li class="nav-item">
            <a class="nav-link text-dark" href="<?php echo BASE_URL; ?>pages/dashboard/index.php">
                <i class="fas fa-th-large me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark" href="<?php echo BASE_URL; ?>pages/tours/index.php">
                <i class="fas fa-map-marked-alt me-2"></i> ແພັກເກັດທົວ
            </a>
        </li>
        <!-- ເພີ່ມເມນູນີ້ເຂົ້າໄປ -->
        <li class="nav-item">
            <a class="nav-link text-dark" href="<?php echo BASE_URL; ?>pages/customers/index.php">
                <i class="fas fa-users me-2"></i> ຈັດການລູກຄ້າ
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark" href="<?php echo BASE_URL; ?>pages/bookings/index.php">
                <i class="fas fa-calendar-check me-2"></i> ລາຍການຈອງ
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark" href="<?php echo BASE_URL; ?>pages/payments/index.php">
                <i class="fas fa-money-bill-wave me-2 text-success"></i> ການຊຳລະເງິນ
            </a>
        </li>
        <hr>
        <div class="px-3 mb-4 border-bottom pb-3">
            <small class="text-muted d-block">ຜູ້ໃຊ້ງານ:</small>
            <span class="fw-bold text-dark"><?php echo $_SESSION['fullname']; ?></span>
        </div>
        <li class="nav-item">
            <a class="nav-link text-danger" href="<?php echo BASE_URL; ?>logout.php" onclick="return confirm('ຕ້ອງການອອກຈາກລະບົບແທ້ບໍ່?')">
                <i class="fas fa-sign-out-alt me-2"></i> ອອກຈາກລະບົບ
            </a>
        </li>
    </ul>
</nav>