<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ສະໝັກໃຊ້ງານ - Tour Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background: #f4f7f6; }
        .register-card { max-width: 500px; margin-top: 50px; border-radius: 20px; border: none; }
    </style>
</head>
<body>
<div class="container">
    <div class="card register-card mx-auto shadow-lg">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary">ສະໝັກບັນຊີຜູ້ໃຊ້</h3>
                <p class="text-muted small">ປ້ອນຂໍ້ມູນຂອງທ່ານເພື່ອເຂົ້າໃຊ້ງານລະບົບ</p>
            </div>

            <?php if(isset($_GET['error']) && $_GET['error'] == 'user_exists'): ?>
                <div class="alert alert-danger small py-2">ຊື່ຜູ້ໃຊ້ນີ້ມີໃນລະບົບແລ້ວ!</div>
            <?php endif; ?>

            <form action="register_action.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small">ຊື່ເຕັມ (Full Name)</label>
                    <input type="text" name="fullname" class="form-control bg-light border-0" placeholder="ຊື່ ແລະ ນາມສະກຸນ" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">ຊື່ຜູ້ໃຊ້ (Username)</label>
                    <input type="text" name="username" class="form-control bg-light border-0" placeholder="admin123" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">ລະຫັດຜ່ານ (Password)</label>
                    <input type="password" name="password" class="form-control bg-light border-0" placeholder="••••••••" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold small">ຢືນຢັນລະຫັດຜ່ານ (Confirm Password)</label>
                    <input type="password" name="confirm_password" class="form-control bg-light border-0" placeholder="••••••••" required>
                </div>
                
                <button type="submit" name="register" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm fw-bold">
                    <i class="fas fa-user-plus me-1"></i> ລົງທະບຽນ
                </button>
            </form>
            
            <div class="text-center mt-4">
                <p class="small text-muted">ມີບັນຊີແລ້ວບໍ່? <a href="login.php" class="text-primary text-decoration-none fw-bold">ເຂົ້າສູ່ລະບົບ</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>