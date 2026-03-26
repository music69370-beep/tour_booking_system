<?php 
include 'config/db.php'; 
// ຖ້າ Login ແລ້ວ ໃຫ້ເດັ້ງໄປ Dashboard ເລີຍ
if(isset($_SESSION['user_id'])) { 
    header("Location: pages/dashboard/index.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ເຂົ້າສູ່ລະບົບ - Tour Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        
        body {
            font-family: 'Noto Sans Lao', sans-serif;
            background-color: #ffffff;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .login-container {
            width: 100%;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .login-box {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        /* ເບື້ອງຊ້າຍ: ຮູບພາບ */
        .login-illustration {
            flex: 1.2; /* ປັບໃຫ້ຮູບໃຫຍ່ຂຶ້ນໜ້ອຍໜຶ່ງ */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-illustration img {
            max-width: 100%;
            height: auto;
            border-radius: 20px; /* ເພີ່ມຄວາມມົນໃຫ້ຮູບ */
        }

        /* ເບື້ອງຂວາ: ຟອມ */
        .login-form-section {
            flex: 1;
            padding: 40px;
            min-width: 350px;
        }

        .form-title {
            font-weight: 700;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-control {
            padding: 12px;
            font-size: 0.95rem;
            border-radius: 8px;
            background-color: #f8f9fa;
            border: 1px solid #eee;
        }

        .btn-login {
            background-color: #198754;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            border: none;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background-color: #157347;
            transform: translateY(-2px);
            color: white;
        }

        .or-separator {
            display: flex;
            align-items: center;
            text-align: center;
            color: #ccc;
            margin: 20px 0;
        }
        .or-separator::before, .or-separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #eee;
        }
        .or-separator span {
            padding: 0 10px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .social-btn {
            display: flex;
            gap: 10px;
        }
        .btn-fb { background-color: #3b5998; color: white; flex: 1; }
        .btn-tw { background-color: #1da1f2; color: white; flex: 1; }
        .btn-social { padding: 10px; border-radius: 8px; font-size: 0.85rem; border: none; }

        @media (max-width: 768px) {
            .login-illustration { display: none; }
        }

        .swal2-popup { font-family: 'Noto Sans Lao', sans-serif !important; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <!-- ພາກສ່ວນຮູບພາບເບື້ອງຊ້າຍ -->
        <div class="login-illustration">
            <!-- *** ຈຸດແກ້ໄຂ: ໃສ່ Path ຮູບຂອງເຈົ້າ *** -->
            <img src="img/registrarse.jpg" alt="Login Image">
        </div>

        <!-- ພາກສ່ວນຟອມເບື້ອງຂວາ -->
        <div class="login-form-section">
            <h3 class="form-title">
                <i class="fas fa-home text-success"></i> ໜ້າເຂົ້າສູ່ລະບົບ ບໍລິຫານຈອງທົວ
            </h3>

            <form action="auth_action.php" method="POST">
                <div class="mb-4">
                    <input type="text" name="username" class="form-control" placeholder="ກະລຸນາປ້ອນຊື່..." required>
                    <label class="small mt-1 text-muted"><i class="fas fa-user-circle"></i> ຊື່ຜູ້ໃຊ້ງານ</label>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="ກະລຸນາປ້ອນລະຫັດ..." required>
                    <label class="small mt-1 text-muted"><i class="fas fa-key"></i> ລະຫັດຜ່ານ</label>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember">
                        <label class="form-check-label small" for="remember">ຈື່ຂ້ອຍ</label>
                    </div>
                    <a href="#" class="text-decoration-none small text-primary">ລືມລະຫັດຜ່ານບໍ?</a>
                </div>

                <button type="submit" name="login" class="btn btn-login w-100">ເຂົ້າສູ່ລະບົບ</button>
            </form>

            <div class="or-separator">
                <span>OR</span>
            </div>

            <div class="social-btn">
                <button class="btn-social btn-fb"><i class="fab fa-facebook-f me-2"></i> Facebook</button>
                <button class="btn-social btn-tw"><i class="fab fa-twitter me-2"></i> Twitter</button>
            </div>

            <div class="text-center mt-4">
                <p class="small text-muted">ຍັງບໍ່ມີບັນຊີບໍ? <a href="register.php" class="text-primary fw-bold text-decoration-none">ສະໝັກສະມາຊິກ</a></p>
                <a href="index.php" class="text-decoration-none small text-muted"><i class="fas fa-arrow-left me-1"></i> ກັບໄປໜ້າເວັບຫຼັກ</a>
            </div>
        </div>
    </div>
</div>

<script>
    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('msg') === 'logout') {
        Toast.fire({ icon: 'info', title: 'ອອກຈາກລະບົບແລ້ວ' });
    } else if (urlParams.get('error')) {
        Toast.fire({ icon: 'error', title: 'ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ!' });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>