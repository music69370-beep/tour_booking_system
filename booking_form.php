<?php 
include 'config/db.php'; 
if(!isset($_GET['tour_id'])) { header("Location: index.php"); exit(); }

$tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);
$res = mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$tour_id' AND status = 'Active'");
$tour = mysqli_fetch_assoc($res);

$tid = $tour['tour_id'];
$booked_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
$booked_count = $booked_res['total'] ?? 0;
$remaining = $tour['max_seats'] - $booked_count;
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈອງທົວ - <?php echo $tour['tour_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; }
        .booking-card { border: none; border-radius: 20px; overflow: hidden; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card booking-card shadow-lg">
                <div class="row g-0">
                    <div class="col-md-4 d-none d-md-block bg-dark">
                        <img src="assets/uploads/tours/<?php echo $tour['image']; ?>" class="h-100 w-100" style="object-fit: cover; opacity: 0.8;">
                    </div>
                    <div class="col-md-8 p-4 p-md-5 bg-white">
                        <h3 class="fw-bold text-primary mb-4">ຢືນຢັນການຈອງທົວ</h3>
                        
                        <form action="process_booking.php" method="POST">
                            <input type="hidden" name="tour_id" value="<?php echo $tour['tour_id']; ?>">
                            <input type="hidden" name="price" id="tour_price" value="<?php echo $tour['price']; ?>">

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">ຊື່ຜູ້ຈອງ (ຫົວໜ້າກຸ່ມ)</label>
                                    <input type="text" name="fullname" class="form-control bg-light border-0" placeholder="ປ້ອນຊື່ຂອງທ່ານ..." required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">ເບີໂທລະສັບ (WhatsApp)</label>
                                    <input type="text" name="phone" class="form-control bg-light border-0" placeholder="020..." required>
                                </div>
                            </div>

                            <div class="row align-items-end mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">ຈຳນວນຄົນທັງໝົດ</label>
                                    <input type="number" name="num_people" id="num_people" class="form-control fw-bold text-primary" value="1" min="1" max="<?php echo $remaining; ?>" oninput="generateParticipantFields(); updateTotal();" required>
                                </div>
                                <div class="col-md-8 text-md-end">
                                    <small class="text-muted d-block">ລາຄາລວມທັງໝົດ:</small>
                                    <h2 class="text-danger fw-bold mb-0" id="total_display"><?php echo number_format($tour['price']); ?> ກີບ</h2>
                                    <input type="hidden" name="total_price" id="total_val" value="<?php echo $tour['price']; ?>">
                                </div>
                            </div>

                            <!-- ສ່ວນລາຍຊື່ຜູ້ຮ່ວມທາງ -->
                            <div id="participant_section" class="mt-4 p-4 border rounded-4 bg-light" style="display:none;">
                                <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-users me-2"></i>ຂໍ້ມູນຜູ້ຮ່ວມເດີນທາງ</h6>
                                <div id="participant_inputs">
                                    <!-- JS ຈະສ້າງ input ບ່ອນນີ້ -->
                                </div>
                            </div>

                            <div class="mt-5">
                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow fw-bold">
                                    <i class="fas fa-check-circle me-2"></i> ສົ່ງຂໍ້ມູນການຈອງ
                                </button>
                                <a href="index.php" class="btn btn-link w-100 text-muted mt-2">ຍົກເລີກ</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateParticipantFields() {
    const num = parseInt(document.getElementById('num_people').value);
    const container = document.getElementById('participant_inputs');
    const section = document.getElementById('participant_section');
    
    container.innerHTML = '';
    
    if (num > 1) {
        section.style.display = 'block';
        for (let i = 2; i <= num; i++) {
            container.innerHTML += `
                <div class="row g-2 mb-3 pb-2 border-bottom">
                    <div class="col-md-7">
                        <label class="small text-muted">ຄົນທີ ${i}: ຊື່ ແລະ ນາມສະກຸນ</label>
                        <input type="text" name="participant_names[]" class="form-control form-control-sm border-0 shadow-sm" placeholder="ລະບຸຊື່..." required>
                    </div>
                    <div class="col-md-5">
                        <label class="small text-muted">ເບີໂທຕິດຕໍ່</label>
                        <input type="text" name="participant_phones[]" class="form-control form-control-sm border-0 shadow-sm" placeholder="020..." required>
                    </div>
                </div>
            `;
        }
    } else {
        section.style.display = 'none';
    }
}

function updateTotal() {
    let price = document.getElementById('tour_price').value;
    let num = document.getElementById('num_people').value;
    let total = price * num;
    document.getElementById('total_display').innerText = new Intl.NumberFormat().format(total) + " ກີບ";
    document.getElementById('total_val').value = total;
}
</script>

</body>
</html>