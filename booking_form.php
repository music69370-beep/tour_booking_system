<?php 
include 'config/db.php'; 

// 1. ກວດສອບ ID ທົວທີ່ສົ່ງມາ
if(!isset($_GET['tour_id'])) {
    header("Location: index.php");
    exit();
}

$tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);

// 2. ດຶງຂໍ້ມູນທົວ
$res = mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$tour_id' AND status = 'Active'");
$tour = mysqli_fetch_assoc($res);

if(!$tour) {
    echo "<script>alert('ບໍ່ພົບຂໍ້ມູນແພັກເກັດທົວ'); window.location='index.php';</script>";
    exit();
}

// 3. ຄຳນວນບ່ອນນັ່ງຫວ່າງ
$booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = '$tour_id' AND status != 'Cancelled'"));
$booked_count = $booked_res['total'] ?? 0;
$remaining = $tour['max_seats'] - $booked_count;

// ຖ້າເຕັມແລ້ວໃຫ້ເດັ້ງກັບ
if($remaining <= 0) {
    echo "<script>alert('ຂໍອະໄພ! ທົວນີ້ເຕັມແລ້ວ'); window.location='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈອງທົວ - <?php echo $tour['tour_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; }
        .booking-card { border: none; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .summary-card { border: none; border-radius: 25px; position: sticky; top: 20px; overflow: hidden; }
        .form-control, .form-select { border-radius: 12px; padding: 12px 15px; border: 1px solid #e0e0e0; }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
        .participant-row { background: #f8f9fa; border-radius: 15px; padding: 15px; margin-bottom: 10px; border: 1px solid #eee; }
        .price-box { background: #fff5f6; border-radius: 15px; padding: 20px; border: 2px dashed #ff4757; }
    </style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php"><i class="fas fa-arrow-left me-2"></i> ກັບໄປໜ້າຫຼັກ</a>
    </div>
</nav>

<div class="container mb-5">
    <div class="row g-4">
        <!-- ເບື້ອງຊ້າຍ: ຟອມກອກຂໍ້ມູນ -->
        <div class="col-lg-7">
            <div class="card booking-card p-4 p-md-5 bg-white">
                <h3 class="fw-bold text-dark mb-4"><i class="fas fa-id-card text-primary me-2"></i>ຂໍ້ມູນການຈອງຂອງທ່ານ</h3>
                
                <form action="process_booking.php" method="POST" id="bookingForm">
                    <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                    <input type="hidden" name="price" id="tour_price" value="<?php echo $tour['price']; ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ຊື່ ແລະ ນາມສະກຸນ (ຫົວໜ້າກຸ່ມ)</label>
                            <input type="text" name="fullname" class="form-control" placeholder="ປ້ອນຊື່ແທ້ຂອງທ່ານ..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ເບີໂທລະສັບ (WhatsApp)</label>
                            <input type="text" name="phone" class="form-control" placeholder="020..." required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">ອີເມວ (Email)</label>
                            <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary">ວັນທີເດີນທາງ</label>
                            <input type="date" name="travel_date" class="form-control border-primary" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ຈຳນວນຄົນທັງໝົດ</label>
                            <input type="number" name="num_people" id="num_people" class="form-control fw-bold" value="1" min="1" max="<?php echo $remaining; ?>" oninput="generateParticipants(); updateTotal();" required>
                            <small class="text-muted">ສາມາດຈອງໄດ້ອີກ: <?php echo $remaining; ?> ບ່ອນ</small>
                        </div>
                    </div>

                    <!-- ລາຍຊື່ຜູ້ຮ່ວມທາງ -->
                    <div id="participant_section" class="mt-5" style="display:none;">
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-users text-primary me-2"></i>ລາຍຊື່ຜູ້ຮ່ວມເດີນທາງ</h5>
                        <div id="participant_inputs">
                            <!-- JS ສ້າງ input ບ່ອນນີ້ -->
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label fw-bold">ໝາຍເຫດ (ເຊັ່ນ: ແພ້ອາຫານ, ຕ້ອງການສິ່ງໃດເພີ່ມເຕີມ)</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="ລະບຸລາຍລະອຽດ..."></textarea>
                    </div>

                    <div class="mt-5 border-top pt-4 text-end">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow">
                            ຢືນຢັນການຈອງ ແລະ ໄປໜ້າຊຳລະເງິນ <i class="fas fa-chevron-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ເບື້ອງຂວາ: ສະຫຼຸບແພັກເກັດ -->
        <div class="col-lg-5">
            <div class="card summary-card shadow-lg bg-white">
                <img src="assets/uploads/tours/<?php echo $tour['image']; ?>" class="w-100" style="height: 250px; object-fit: cover;">
                <div class="card-body p-4">
                    <span class="badge bg-primary mb-2"><?php echo $tour['category']; ?></span>
                    <h4 class="fw-bold text-dark mb-3"><?php echo $tour['tour_name']; ?></h4>
                    
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span><i class="far fa-clock me-1"></i> ໄລຍະເວລາ:</span>
                        <span><?php echo $tour['duration']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span><i class="fas fa-utensils me-1"></i> ອາຫານ:</span>
                        <span><?php echo $tour['meals']; ?> ຄາບ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-muted">
                        <span><i class="fas fa-map-marker-alt me-1 text-danger"></i> ຈຸດນັດພົບ:</span>
                        <span class="text-end"><?php echo $tour['meeting_point']; ?></span>
                    </div>

                    <hr>

                    <h6 class="fw-bold text-dark mb-2">ສິ່ງທີ່ລວມຢູ່ນຳ:</h6>
                    <div class="small text-muted mb-4" style="white-space: pre-line;"><?php echo $tour['whats_included']; ?></div>

                    <div class="price-box text-center">
                        <p class="text-muted mb-1 small fw-bold text-uppercase">ລາຄາລວມທັງໝົດ</p>
                        <h1 class="text-danger fw-bold mb-0" id="display_total"><?php echo number_format($tour['price']); ?></h1>
                        <p class="mb-0 fw-bold text-danger">ກີບ</p>
                    </div>
                    
                    <div class="mt-4 alert alert-info border-0 small">
                        <i class="fas fa-info-circle me-2"></i> ຫຼັງຈາກກົດຢືນຢັນ ລະບົບຈະພາທ່ານໄປໜ້າສະແກນ QR Code ເພື່ອຊຳລະເງິນ.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateParticipants() {
    const num = parseInt(document.getElementById('num_people').value);
    const container = document.getElementById('participant_inputs');
    const section = document.getElementById('participant_section');
    const max = <?php echo $remaining; ?>;
    
    // ປ້ອງກັນການກອກເກີນບ່ອນນັ່ງຫວ່າງ
    if (num > max) {
        alert("ຂໍອະໄພ! ບ່ອນນັ່ງຫວ່າງເຫຼືອພຽງ " + max + " ບ່ອນ");
        document.getElementById('num_people').value = max;
        generateParticipants();
        return;
    }

    container.innerHTML = '';
    
    if (num > 1) {
        section.style.display = 'block';
        for (let i = 2; i <= num; i++) {
            container.innerHTML += `
                <div class="participant-row">
                    <div class="row g-2">
                        <div class="col-md-7">
                            <label class="small fw-bold text-muted">ຄົນທີ ${i}: ຊື່ ແລະ ນາມສະກຸນ</label>
                            <input type="text" name="participant_names[]" class="form-control form-control-sm border-0 shadow-sm" placeholder="ລະບຸຊື່..." required>
                        </div>
                        <div class="col-md-5">
                            <label class="small fw-bold text-muted">ເບີໂທລະສັບ</label>
                            <input type="text" name="participant_phones[]" class="form-control form-control-sm border-0 shadow-sm" placeholder="020..." required>
                        </div>
                    </div>
                </div>
            `;
        }
    } else {
        section.style.display = 'none';
    }
}

function updateTotal() {
    const price = document.getElementById('tour_price').value;
    const num = document.getElementById('num_people').value;
    const total = price * num;
    document.getElementById('display_total').innerText = new Intl.NumberFormat().format(total);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>