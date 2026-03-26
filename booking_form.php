<?php 
include 'config/db.php'; 

// 1. ກວດສອບ ID ທົວ
if(!isset($_GET['tour_id'])) {
    header("Location: index.php");
    exit();
}

$tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);
$res = mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$tour_id' AND status = 'Active'");
$tour = mysqli_fetch_assoc($res);

if(!$tour) {
    echo "ບໍ່ພົບຂໍ້ມູນແພັກເກັດທົວ";
    exit();
}

// 2. ຄຳນວນບ່ອນນັ່ງຫວ່າງອີກຄັ້ງ (ເພື່ອຄວາມປອດໄພ)
$tid = $tour['tour_id'];
$booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
$booked_count = $booked_res['total'] ?? 0;
$remaining = $tour['max_seats'] - $booked_count;

// ຖ້າບັງເອີນເຕັມພໍດີ ໃຫ້ເດັ້ງກັບ
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
        .booking-card { border: none; border-radius: 20px; overflow: hidden; }
        .tour-info-box { background-color: #e7f1ff; border-radius: 15px; padding: 20px; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card booking-card shadow-lg">
                <div class="row g-0">
                    <div class="col-md-5 d-none d-md-block">
                        <img src="assets/uploads/tours/<?php echo $tour['image']; ?>" class="h-100 w-100" style="object-fit: cover;">
                    </div>
                    <div class="col-md-7 p-4 p-md-5 bg-white">
                        <h3 class="fw-bold text-primary mb-4">ຢືນຢັນການຈອງທົວ</h3>
                        
                        <div class="tour-info-box mb-4 border-start border-primary border-4">
                            <h5 class="fw-bold mb-1"><?php echo $tour['tour_name']; ?></h5>
                            <p class="text-muted small mb-0"><i class="far fa-clock me-1"></i> ໄລຍະເວລາ: <?php echo $tour['duration']; ?></p>
                            <h4 class="text-danger fw-bold mt-2 mb-0"><?php echo number_format($tour['price']); ?> <small style="font-size: 0.9rem;">ກີບ/ຄົນ</small></h4>
                            <small class="text-success fw-bold"><i class="fas fa-chair me-1"></i> ຫວ່າງ: <?php echo $remaining; ?> ບ່ອນ</small>
                        </div>

                        <form action="process_booking.php" method="POST">
                            <input type="hidden" name="tour_id" value="<?php echo $tour['tour_id']; ?>">
                            <input type="hidden" name="price" id="tour_price" value="<?php echo $tour['price']; ?>">

                            <div class="mb-3">
                                <label class="form-label fw-bold">ຊື່ ແລະ ນາມສະກຸນ</label>
                                <input type="text" name="fullname" class="form-control form-control-lg rounded-pill shadow-sm" placeholder="ປ້ອນຊື່ຂອງທ່ານ..." required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">ເບີໂທລະສັບ (WhatsApp)</label>
                                <input type="text" name="phone" class="form-control form-control-lg rounded-pill shadow-sm" placeholder="020..." required>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="form-label fw-bold">ຈຳນວນຄົນ</label>
                                    <input type="number" name="num_people" id="num_people" 
                                           class="form-control form-control-lg rounded-pill text-center shadow-sm" 
                                           value="1" min="1" max="<?php echo $remaining; ?>" 
                                           oninput="updateTotal()" required>
                                    <small class="text-muted text-center d-block mt-1">ຈອງໄດ້ບໍ່ເກີນ <?php echo $remaining; ?> ບ່ອນ</small>
                                </div>
                                <div class="col-6 text-end">
                                    <label class="form-label fw-bold">ລາຄາລວມ</label>
                                    <h3 class="text-primary fw-bold" id="total_display"><?php echo number_format($tour['price']); ?></h3>
                                    <input type="hidden" name="total_price" id="total_val" value="<?php echo $tour['price']; ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow">
                                <i class="fas fa-check-circle me-2"></i> ສົ່ງຂໍ້ມູນການຈອງ
                            </button>
                            <a href="index.php" class="btn btn-link w-100 text-muted mt-2">ຍົກເລີກ</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateTotal() {
    let price = document.getElementById('tour_price').value;
    let numInput = document.getElementById('num_people');
    let max = parseInt(numInput.max);
    let num = parseInt(numInput.value);

    // ປ້ອງກັນການປ້ອນເກີນບ່ອນນັ່ງຫວ່າງ
    if(num > max) {
        alert("ຂໍອະໄພ! ບ່ອນນັ່ງບໍ່ພໍ, ທ່ານສາມາດຈອງໄດ້ສູງສຸດ " + max + " ບ່ອນ");
        numInput.value = max;
        num = max;
    }
    if(num < 1 || isNaN(num)) num = 1;

    let total = price * num;
    document.getElementById('total_display').innerText = new Intl.NumberFormat().format(total);
    document.getElementById('total_val').value = total;
}
</script>

</body>
</html>