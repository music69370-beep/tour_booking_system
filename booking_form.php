<?php 
include 'config/db.php'; 
if(!isset($_GET['tour_id'])) { header("Location: index.php"); exit(); }

$tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);
$res = mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$tour_id' AND status = 'Active'");
$tour = mysqli_fetch_assoc($res);

// ຄຳນວນບ່ອນນັ່ງ
$booked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tour_id AND status != 'Cancelled'"));
$remaining = $tour['max_seats'] - ($booked['total'] ?? 0);
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
        .summary-card { border-radius: 20px; border: none; position: sticky; top: 100px; }
        .form-card { border-radius: 20px; border: none; }
        .price-total { font-size: 2rem; color: #ff4757; font-weight: 700; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row g-4">
        <!-- ເບື້ອງຊ້າຍ: ຟອມກອກຂໍ້ມູນ -->
        <div class="col-lg-7">
            <div class="card form-card shadow-sm p-4 p-md-5 bg-white">
                <h3 class="fw-bold text-primary mb-4"><i class="fas fa-edit me-2"></i>ຂໍ້ມູນການຈອງ</h3>
                
                <form action="process_booking.php" method="POST">
                    <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                    <input type="hidden" name="price" id="tour_price" value="<?php echo $tour['price']; ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ຊື່ ແລະ ນາມສະກຸນ (ຫົວໜ້າກຸ່ມ)</label>
                            <input type="text" name="fullname" class="form-control bg-light border-0 py-2" placeholder="ປ້ອນຊື່ຂອງທ່ານ..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ເບີໂທລະສັບ (WhatsApp)</label>
                            <input type="text" name="phone" class="form-control bg-light border-0 py-2" placeholder="020..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ວັນທີເດີນທາງ</label>
                            <input type="date" name="travel_date" class="form-control py-2 border-primary" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ຈຳນວນຄົນ</label>
                            <input type="number" name="num_people" id="num_people" class="form-control py-2" value="1" min="1" max="<?php echo $remaining; ?>" oninput="generateParticipants(); updateTotal();" required>
                            <small class="text-muted">ຫວ່າງ: <?php echo $remaining; ?> ບ່ອນ</small>
                        </div>
                    </div>

                    <!-- Participant Names -->
                    <div id="participant_section" class="mt-4" style="display:none;">
                        <h6 class="fw-bold text-primary mb-3">ລາຍຊື່ຜູ້ຮ່ວມທາງ</h6>
                        <div id="participant_inputs"></div>
                    </div>

                    <!-- Notes -->
                    <div class="mt-4">
                        <label class="form-label fw-bold small text-muted">ໝາຍເຫດ (ເຊັ່ນ: ແພ້ອາຫານ ຫຼື ສິ່ງທີ່ຕ້ອງການພິເສດ)</label>
                        <textarea name="note" class="form-control bg-light border-0" rows="3" placeholder="ລະບຸທີ່ນີ້..."></textarea>
                    </div>

                    <div class="mt-5 border-top pt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-lg fw-bold py-3">ຢືນຢັນການຈອງ</button>
                        <a href="index.php" class="btn btn-link w-100 text-muted mt-2 text-decoration-none">ຍົກເລີກ</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- ເບື້ອງຂວາ: ສະຫຼຸບແພັກເກັດ -->
        <div class="col-lg-5">
            <div class="card summary-card shadow-lg overflow-hidden">
                <img src="assets/uploads/tours/<?php echo $tour['image']; ?>" class="w-100" style="height: 200px; object-fit: cover;">
                <div class="card-body p-4 bg-white">
                    <span class="badge bg-primary mb-2"><?php echo $tour['category']; ?></span>
                    <h4 class="fw-bold"><?php echo $tour['tour_name']; ?></h4>
                    <p class="text-muted small"><i class="fas fa-map-marker-alt text-danger me-2"></i>ນັດພົບ: <?php echo $tour['meeting_point']; ?></p>
                    
                    <hr>
                    
                    <h6 class="fw-bold text-success small"><i class="fas fa-check-circle me-2"></i>ສິ່ງທີ່ທ່ານຈະໄດ້ຮັບ:</h6>
                    <div class="small text-muted mb-3" style="white-space: pre-line;"><?php echo $tour['whats_included']; ?></div>
                    
                    <div class="bg-light p-3 rounded-4 mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ລາຄາຕໍ່ຄົນ:</span>
                            <span class="fw-bold"><?php echo number_format($tour['price']); ?> ກີບ</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">ລາຄາລວມທັງໝົດ:</span>
                            <span class="price-total" id="display_total"><?php echo number_format($tour['price']); ?></span>
                        </div>
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
    container.innerHTML = '';
    if (num > 1) {
        section.style.display = 'block';
        for (let i = 2; i <= num; i++) {
            container.innerHTML += `
                <div class="row g-2 mb-2">
                    <div class="col-7"><input type="text" name="participant_names[]" class="form-control form-control-sm border-0 bg-light" placeholder="ຊື່ຄົນທີ ${i}" required></div>
                    <div class="col-5"><input type="text" name="participant_phones[]" class="form-control form-control-sm border-0 bg-light" placeholder="ເບີໂທ" required></div>
                </div>`;
        }
    } else { section.style.display = 'none'; }
}

function updateTotal() {
    const price = document.getElementById('tour_price').value;
    const num = document.getElementById('num_people').value;
    const total = price * num;
    document.getElementById('display_total').innerText = new Intl.NumberFormat().format(total);
}
</script>
</body>
</html>