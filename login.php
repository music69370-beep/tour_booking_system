<?php 
include 'config/db.php'; 
// ຖ້າ Login ແລ້ວ ໃຫ້ເດັ້ງໄປໜ້າ Dashboard ເລີຍ
if(isset($_SESSION['user_id'])) { header("Location: pages/dashboard/index.php"); }
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ເຂົ້າສູ່ລະບົບ - Tour Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background: #f4f7f6; }
        .login-card { max-width: 400px; margin-top: 100px; border-radius: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card login-card mx-auto shadow-lg border-0">
        <div class="card-body p-5">
            <h3 class="text-center fw-bold text-primary mb-4">ເຂົ້າສູ່ລະບົບ</h3>
            <form action="auth_action.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">ຊື່ຜູ້ໃຊ້ (Username)</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">ລະຫັດຜ່ານ (Password)</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 py-2 rounded-pill shadow">ເຂົ້າສູ່ລະບົບ</button>
            </form>
            <?php if(isset($_GET['error'])) echo '<p class="text-danger text-center mt-3">ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ!</p>'; ?>
        </div>
    </div>
</div>
</body>
</html>