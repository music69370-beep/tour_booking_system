<?php 
include 'config/db.php'; 
if(isset($_SESSION['user_id'])) { header("Location: pages/dashboard/index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ເຂົ້າສູ່ລະບົບ - Tour Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background: #f4f7f6; }
        .login-card { max-width: 400px; margin-top: 100px; border-radius: 20px; border: none; }
        /* ປັບ Font ໃຫ້ SweetAlert */
        .swal2-popup { font-family: 'Noto Sans Lao', sans-serif !important; font-size: 0.9rem !important; }
    </style>
</head>
<body>
<div class="container">
    <div class="card login-card mx-auto shadow-lg">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 d-inline-block p-3 rounded-circle mb-3">
                    <i class="fas fa-user-shield fa-3x text-primary"></i>
                </div>
                <h3 class="fw-bold">ເຂົ້າສູ່ລະບົບ</h3>
            </div>

            <form action="auth_action.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small">ຊື່ຜູ້ໃຊ້</label>
                    <input type="text" name="username" class="form-control bg-light border-0 shadow-none" placeholder="Username" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold small">ລະຫັດຜ່ານ</label>
                    <input type="password" name="password" class="form-control bg-light border-0 shadow-none" placeholder="••••••••" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 shadow-sm py-2 rounded-pill fw-bold">ເຂົ້າສູ່ລະບົບ</button>
            </form>
            
            <div class="text-center mt-4">
                <p class="small text-muted">ຍັງບໍ່ມີບັນຊີບໍ? <a href="register.php" class="text-primary text-decoration-none fw-bold">ສະໝັກສະມາຊິກ</a></p>
                <a href="index.php" class="text-decoration-none small text-muted"><i class="fas fa-arrow-left me-1"></i> ກັບໄປໜ້າເວັບຫຼັກ</a>
            </div>
        </div>
    </div>
</div>

<script>
// 1. ຕັ້ງຄ່າ Toast (ແຈ້ງເຕືອນມຸມຂວາເທິງ)
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});

// 2. ກວດເຊັກ Parameter "msg" ຈາກ URL
const urlParams = new URLSearchParams(window.location.search);
const msg = urlParams.get('msg');

if (msg === 'registered') {
    Toast.fire({
        icon: 'success',
        title: 'ສະໝັກສະມາຊິກສຳເລັດແລ້ວ!',
        text: 'ກະລຸນາເຂົ້າສູ່ລະບົບເພື່ອເລີ່ມໃຊ້ງານ'
    });
} else if (msg === 'logout') {
    Toast.fire({ icon: 'info', title: 'ອອກຈາກລະບົບແລ້ວ' });
} else if (urlParams.get('error')) {
    Toast.fire({ icon: 'error', title: 'ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ!' });
}
</script>

</body>
</html>