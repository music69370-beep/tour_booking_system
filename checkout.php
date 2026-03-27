<?php 
include 'config/db.php'; 

if(!isset($_GET['booking_id'])) { header("Location: index.php"); exit(); }

$booking_id = mysqli_real_escape_string($conn, $_GET['booking_id']);

// ດຶງຂໍ້ມູນການຈອງມາໂຊ
$sql = "SELECT b.*, c.fullname, t.tour_name, t.price 
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- QRCode.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; }
        .checkout-card { border: none; border-radius: 25px; }
        .qr-container { background: white; padding: 20px; border-radius: 20px; display: inline-block; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .price-box { background: #fff5f6; border: 2px dashed #ff4757; border-radius: 15px; padding: 15px; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card checkout-card shadow-lg p-4 p-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">ຊຳລະເງິນ</h2>
                    <p class="text-muted">ເລກທີການຈອງ: #BK-<?php echo $booking_id; ?></p>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>ລູກຄ້າ:</span>
                        <span class="fw-bold"><?php echo $data['fullname']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>ທົວ:</span>
                        <span class="fw-bold"><?php echo $data['tour_name']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>ຈຳນວນ:</span>
                        <span class="fw-bold"><?php echo $data['num_people']; ?> ຄົນ</span>
                    </div>
                    <div class="price-box text-center mt-3">
                        <small class="text-muted d-block">ຍອດເງິນທີ່ຕ້ອງຊຳລະ</small>
                        <h2 class="text-danger fw-bold mb-0"><?php echo number_format($data['total_price']); ?> ກີບ</h2>
                    </div>
                </div>

                <!-- ສ່ວນສະແດງ QR Code -->
                <div class="text-center my-4">
                    <div class="qr-container">
                        <div id="qrcode"></div>
                        <div class="mt-3">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/BCEL_One_Logo.png" height="30" alt="BCEL One">
                            <p class="small fw-bold mb-0 mt-1">Scan to Pay</p>
                        </div>
                    </div>
                    <p class="small text-muted mt-3"><i class="fas fa-info-circle me-1"></i> ລະບົບໄດ້ລະບຸຍອດເງິນໃນ QR ນີ້ໃຫ້ທ່ານແລ້ວ</p>
                </div>

                <!-- ຟອມອັບໂຫລດໃບບິນ -->
                <form action="pages/payments/save.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                    <input type="hidden" name="payment_method" value="BCEL One (Scan QR)">
                    <input type="hidden" name="payment_date" value="<?php echo date('Y-m-d H:i:s'); ?>">
                    <!-- ສົ່ງມາຈາກໜ້າບ້ານ -->
                    <input type="hidden" name="from_customer" value="1"> 

                    <div class="mb-4">
                        <label class="form-label fw-bold"><i class="fas fa-cloud-upload-alt me-2"></i>ອັບໂຫລດໃບບິນ (Slip)</label>
                        <input type="file" name="payment_slip" class="form-control" accept="image/*" required>
                    </div>

                    <button type="submit" name="save_payment" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow">
                        ຢືນຢັນການສົ່ງຫຼັກຖານ
                    </button>
                    <a href="index.php" class="btn btn-link w-100 text-muted mt-2 text-decoration-none">ຈ່າຍພາຍຫຼັງ</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // ສ້າງ QR Code
    // ໃນຄວາມເປັນຈິງ ທ່ານຕ້ອງໃຊ້ LaoQR String ຂອງທະນາຄານ
    // ບົດຮຽນນີ້ເຮົາຈະສ້າງ QR ທີ່ເກັບຂໍ້ຄວາມລາຍລະອຽດການຈອງ
    var qrData = "BookingID: BK-<?php echo $booking_id; ?> | Amount: <?php echo $data['total_price']; ?> LAK";
    
    new QRCode(document.getElementById("qrcode"), {
        text: qrData,
        width: 200,
        height: 200,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>

</body>
</html>