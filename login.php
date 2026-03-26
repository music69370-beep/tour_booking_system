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
    <title>ເຂົ້າສູ່ລະບົບ - Tour Booking System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-color: #00b894; /* ສີຂຽວ Mint ທີ່ທັນສະໄໝ */
            --bg-page: #f0f2f5;       /* ສີພື້ນຫຼັງນອກ Card */
            --bg-form: #f8f9fa;       /* ສີເທົາອ່ອນສຳລັບເບື້ອງຟອມ */
            --text-dark: #2d3436;
        }

        body {
            font-family: 'Noto Sans Lao', sans-serif;
            background-color: var(--bg-page);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            width: 100%;
            max-width: 1000px; /* ຂະໜາດພໍດີພໍງາມ */
            min-height: 620px;
        }

        /* ເບື້ອງຊ້າຍ: ສີຂາວລ້ວນ (Illustration) */
        .illustration-side {
            background-color: #ffffff;
            flex: 1.1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px;
            text-align: center;
        }

        .illustration-side img {
            max-width: 90%;
            height: auto;
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* ເບື້ອງຂວາ: ສີເທົາອ່ອນ (Form) */
        .form-side {
            background-color: var(--bg-form);
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-left: 1px solid #edf2f7;
        }

        .brand-section {
            margin-bottom: 40px;
        }

        .brand-logo {
            color: var(--primary-color);
            font-size: 26px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            margin-top: 10px;
        }

        /* Styling Inputs */
        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #b2bec3;
            transition: 0.3s;
        }

        .form-control {
            height: 54px;
            padding-left: 48px;
            border-radius: 14px;
            border: 1.5px solid #e9ecef;
            background-color: #ffffff; /* Input ເປັນສີຂາວຕັດກັບພື້ນເທົາ */
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 184, 148, 0.1);
            background-color: #ffffff;
        }

        .form-control:focus + i {
            color: var(--primary-color);
        }

        /* Buttons */
        .btn-login {
            background-color: var(--primary-color);
            border: none;
            height: 54px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
            box-shadow: 0 10px 20px rgba(0, 184, 148, 0.2);
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: #00a383;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(0, 184, 148, 0.3);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0;
            color: #adb5bd;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e9ecef;
        }
        .divider span { padding: 0 15px; }

        .social-area {
            display: flex;
            gap: 15px;
        }

        .btn-social {
            flex: 1;
            height: 48px;
            border-radius: 12px;
            border: 1px solid #dee2e6;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--text-dark);
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .btn-social:hover {
            background-color: #f1f3f5;
            border-color: #ced4da;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .illustration-side { display: none; }
            .login-card { max-width: 480px; }
            .form-side { border-left: none; padding: 40px; }
        }

        .swal2-popup { font-family: 'Noto Sans Lao', sans-serif !important; border-radius: 20px; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="illustration-side">
        <img src="img/registrarse.jpg" alt="Login Illustration">
        <div class="mt-4">
            <h4 class="fw-bold mb-2">ສະບາຍດີ! ຍິນດີຕ້ອນຮັບ</h4>
            <p class="text-muted small px-5">ເຂົ້າສູ່ລະບົບເພື່ອຈັດການຂໍ້ມູນການຈອງທົວຂອງທ່ານແບບມືອາຊີບ</p>
        </div>
    </div>

    <div class="form-side">
        <div class="brand-section">
            <div class="brand-logo">
                <i class="fas fa-globe-asia"></i> <span>TourSystem</span>
            </div>
            <h2 class="form-title">ເຂົ້າສູ່ລະບົບ</h2>
            <p class="text-muted small">ກະລຸນາປ້ອນຂໍ້ມູນບັນຊີຂອງທ່ານ</p>
        </div>

        <form action="auth_action.php" method="POST">
            <div class="input-box">
                <input type="text" name="username" class="form-control" placeholder="ຊື່ຜູ້ໃຊ້ງານ" required>
                <i class="fas fa-user"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" class="form-control" placeholder="ລະຫັດຜ່ານ" required>
                <i class="fas fa-lock"></i>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label small text-muted" for="remember">ຈື່ຂ້ອຍໄວ້</label>
                </div>
                <a href="#" class="text-decoration-none small fw-medium" style="color: var(--primary-color);">ລືມລະຫັດຜ່ານ?</a>
            </div>

            <button type="submit" name="login" class="btn btn-login w-100">ເຂົ້າສູ່ລະບົບ</button>
        </form>

        <div class="divider">
            <span>ຫຼື ເຂົ້າໃຊ້ງານຜ່ານ</span>
        </div>

        <div class="social-area">
            <a href="#" class="btn-social">
                <img src="https://www.google.com/favicon.ico" width="18" class="me-2"> Google
            </a>
            <a href="#" class="btn-social">
                <i class="fab fa-facebook text-primary me-2"></i> Facebook
            </a>
        </div>

        <div class="text-center mt-5">
            <p class="small text-muted">ຍັງບໍ່ມີບັນຊີບໍ? <a href="register.php" class="fw-bold text-decoration-none" style="color: var(--primary-color);">ສະໝັກສະມາຊິກ</a></p>
            <a href="index.php" class="text-decoration-none small text-muted"><i class="fas fa-arrow-left me-1"></i> ກັບໄປໜ້າຫຼັກ</a>
        </div>
    </div>
</div>

<script>
    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('msg') === 'logout') {
        Toast.fire({ icon: 'info', title: 'ທ່ານໄດ້ອອກຈາກລະບົບແລ້ວ' });
    } else if (urlParams.get('error')) {
        Toast.fire({ icon: 'error', title: 'ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ!' });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>