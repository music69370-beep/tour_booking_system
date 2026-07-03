<?php 
include 'config/db.php'; 

if(!isset($_GET['booking_id'])) { header("Location: index.php"); exit(); }

$booking_id = mysqli_real_escape_string($conn, $_GET['booking_id']);

// ດຶງຂໍ້ມູນການຈອງ
$sql = "SELECT b.*, c.fullname, t.tour_name, t.image as tour_image
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        WHERE b.booking_id = '$booking_id'";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

if(!$data) { echo "ບໍ່ພົບຂໍ້ມູນການຈອງ"; exit(); }
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຊຳລະເງິນ - Tour Booking</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap');
        
        :root {
            --primary-color: #0061ff;
            --bg-body: #f0f2f5;
            --card-radius: 24px;
        }

        body { 
            font-family: 'Noto Sans Lao', sans-serif; 
            background-color: var(--bg-body);
            color: #333;
        }

        .checkout-container {
            max-width: 600px;
            margin: 50px auto;
        }

        .main-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            background: #fff;
        }

        .step-header {
            background: #fff;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #f1f3f7;
        }

        .tour-mini-info {
            background: #f8faff;
            border-radius: 18px;
            padding: 15px;
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .tour-mini-img {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            margin-right: 15px;
        }

        .price-box {
            background: #fff5f6;
            border: 2px dashed #ff4757;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            margin-bottom: 30px;
        }

        .price-label {
            color: #ff4757;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .price-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: #d63031;
            margin: 5px 0;
        }

        .qr-section {
            text-align: center;
            padding: 20px;
            background: #fff;
            position: relative;
        }

        .qr-frame {
            display: inline-block;
            padding: 15px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .bcel-logo {
            height: 35px;
            margin-bottom: 15px;
        }

        /* Styling Form */
        .upload-section {
            padding: 30px;
            background: #fdfdfd;
        }

        .custom-file-upload {
            border: 2px dashed #d1d3e2;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            background: #fff;
        }

        .custom-file-upload:hover {
            border-color: var(--primary-color);
            background: #f0f7ff;
        }

        .btn-submit {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            border: none;
            border-radius: 15px;
            padding: 16px;
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
            width: 100%;
            box-shadow: 0 10px 20px rgba(0, 184, 148, 0.2);
            transition: 0.3s;
            margin-top: 20px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(0, 184, 148, 0.3);
        }

        .back-link {
            text-decoration: none;
            color: #b2bec3;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 20px;
        }

        .back-link:hover { color: #636e72; }
    </style>
</head>
<body>

<div class="container">
    <div class="checkout-container">
        <div class="main-card shadow-lg">
            <!-- Step Header -->
            <div class="step-header">
                <div class="mb-2 text-primary fw-bold">ຂັ້ນຕອນການຊຳລະເງິນ</div>
                <h3 class="fw-bold m-0">Scan QR ເພື່ອຊຳລະ</h3>
            </div>

            <div class="p-4 p-md-5 pt-4">
                <!-- Tour Info -->
                <div class="tour-mini-info">
                    <img src="assets/uploads/tours/<?php echo $data['tour_image']; ?>" class="tour-mini-img shadow-sm">
                    <div>
                        <h6 class="fw-bold mb-1"><?php echo $data['tour_name']; ?></h6>
                        <small class="text-muted"><i class="fas fa-user me-1"></i> ລູກຄ້າ: <?php echo $data['fullname']; ?></small>
                    </div>
                </div>

                <!-- Price Box -->
                <div class="price-box">
                    <div class="price-label">ຍອດເງິນທີ່ຕ້ອງຊຳລະທັງໝົດ</div>
                    <div class="price-value"><?php echo number_format($data['total_price']); ?> <small class="fs-4">ກີບ</small></div>
                    <small class="text-muted">Booking ID: #BK-<?php echo $booking_id; ?></small>
                </div>

                <!-- QR Section -->
                <div class="qr-section">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/BCEL_One_Logo.png" class="bcel-logo" alt="BCEL One">
                    <br>
                    <div class="qr-frame">
                        <div id="qrcode"></div>
                    </div>
                    <div class="mt-3 small text-muted">
                        <i class="fas fa-info-circle me-1"></i> ລະບົບໄດ້ລະບຸຍອດເງິນ ແລະ ເລກທີການຈອງໃນ QR ນີ້ໃຫ້ທ່ານແລ້ວ
                    </div>
                </div>

                <!-- Slip Upload Form -->
                <div class="upload-section border-top mt-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-file-invoice me-2 text-primary"></i> ສົ່ງຫຼັກຖານການໂອນ (ສະລິບ)</h6>
                    
                    <form action="pages/payments/save.php" method="POST" enctype="multipart/form-data">
                        <!-- ຂໍ້ມູນສຳຄັນທີ່ຕ້ອງສົ່ງໄປເບື້ອງຫຼັງ -->
                        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                        <input type="hidden" name="payment_method" value="BCEL One (Scan QR)">
                        <input type="hidden" name="payment_date" value="<?php echo date('Y-m-d H:i:s'); ?>">
                        <input type="hidden" name="from_customer" value="1"> 

                        <div class="mb-3">
                            <input type="file" name="payment_slip" id="payment_slip" class="form-control" accept="image/*" required>
                        </div>
                        
                        <button type="submit" name="save_payment" class="btn btn-submit">
                            ຢືນຢັນການສົ່ງຫຼັກຖານ <i class="fas fa-paper-plane ms-2"></i>
                        </button>
                    </form>

                    <div class="text-center">
                        <a href="index.php" class="back-link"><i class="fas fa-arrow-left me-1"></i> ກັບໄປໜ້າລາຍການທົວ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ສ້າງ QR Code
    // ໝາຍເຫດ: ນີ້ແມ່ນ QR ຕົວຢ່າງ, ໃນການໃຊ້ງານຈິງຄວນໃຊ້ LaoQR String ຂອງທະນາຄານ
    var qrData = "Booking: #BK-<?php echo $booking_id; ?> | Name: <?php echo $data['fullname']; ?> | Amount: <?php echo $data['total_price']; ?> LAK";
    
    new QRCode(document.getElementById("qrcode"), {
        text: qrData,
        width: 180,
        height: 180,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>