<?php include 'config/db.php'; 
/** @var mysqli $conn */
if(!isset($_GET['tour_id'])) { header("Location: index.php"); exit(); }
$tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);
$tour = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$tour_id'"));
if(!$tour) { header("Location: index.php"); exit(); }

$booked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tour_id AND status != 'Cancelled'"));
$remaining = $tour['max_seats'] - ($booked['total'] ?? 0);
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຢືນຢັນການຈອງ - <?php echo $tour['tour_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; }
        .form-card { border-radius: 25px; border: none; }
        .section-title { font-weight: 700; color: #0d6efd; border-bottom: 2px solid #0d6efd; display: inline-block; padding-bottom: 5px; margin-bottom: 20px; font-size: 1.1rem; }
        .seat { width: 45px; height: 45px; background: white; border: 2px solid #cbd5e0; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-weight: bold; transition: 0.2s; font-size: 0.9rem; }
        .seat.selected { background: #0d6efd; color: white; border-color: #0d6efd; }
        .seat.occupied { background: #ff4757; color: white; cursor: not-allowed; border-color: #ff4757; opacity: 0.8; }
        .participant-item { background: #f8f9fc; border-radius: 20px; padding: 20px; border: 1px solid #edf2f7; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container my-5">
    <form action="process_booking.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
        <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
        <input type="hidden" name="price" value="<?php echo $tour['price']; ?>">

        <div class="row g-4">
            <!-- ເບື້ອງຊ້າຍ: ຟອມຂໍ້ມູນ -->
            <div class="col-lg-8">
                <div class="card form-card shadow-sm p-4 p-md-5 bg-white">
                    <h3 class="fw-bold text-dark mb-4"><i class="fas fa-edit text-primary me-2"></i>ຢືນຢັນການຈອງທົວ</h3>
                    
                    <!-- 1. ຂໍ້ມູນຜູ້ຈອງຫຼັກ -->
                    <div class="section-title"><i class="fas fa-user-circle me-2"></i>1. ຂໍ້ມູນສ່ວນຕົວຜູ້ຈອງ (ຫົວໜ້າຄະນະ)</div>
                    <div class="row g-3 mb-5">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">ຊື່ ແລະ ນາມສະກຸນ</label>
                            <input type="text" name="fullname" class="form-control" placeholder="ປ້ອນຊື່ແທ້..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">ເພດ</label>
                            <select name="gender" class="form-select">
                                <option value="Male">ຊາຍ (Male)</option>
                                <option value="Female">ຍິງ (Female)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ວັນເດືອນປີເກີດ</label>
                            <input type="date" name="birthday" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ສັນຊາດ</label>
                            <input type="text" name="nationality" class="form-control" value="Lao" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ເລກບັດປະຈຳຕົວ / ພາສປອດ</label>
                            <input type="text" name="id_card_no" class="form-control" placeholder="ປ້ອນເລກບັດ..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary">ຮູບພາບບັດ (Scan/ຮູບຖ່າຍ)</label>
                            <input type="file" name="id_card_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ເບີໂທລະສັບ (WhatsApp)</label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="020..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ອີເມວ</label>
                            <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">ທີ່ຢູ່ປະຈຸບັນ</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="ບ້ານ, ເມືອງ, ແຂວງ..." required></textarea>
                        </div>
                    </div>

                    <!-- 2. ຕິດຕໍ່ສຸກເສີນ -->
                    <div class="section-title text-danger" style="border-color:#ff4757"><i class="fas fa-phone-alt me-2"></i>2. ຂໍ້ມູນຕິດຕໍ່ສຸກເສີນ</div>
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ຊື່ຜູ້ຕິດຕໍ່ສຸກເສີນ</label>
                            <input type="text" name="emergency_name" class="form-control" placeholder="ຊື່ຍາດພີ່ນ້ອງ..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ເບີໂທສຸກເສີນ</label>
                            <input type="text" name="emergency_phone" class="form-control" placeholder="ເບີໂທ..." required>
                        </div>
                    </div>

                    <!-- 3. ຈຳນວນຄົນ ແລະ ຜູ້ຮ່ວມທາງ -->
                    <div class="section-title text-success" style="border-color:#198754"><i class="fas fa-users me-2"></i>3. ຈຳນວນຄົນ ແລະ ຜູ້ຮ່ວມທາງ</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ຈຳນວນຄົນທັງໝົດ</label>
                            <input type="number" name="num_people" id="num_people" class="form-control border-success fw-bold" value="1" min="1" max="<?php echo $remaining; ?>" oninput="handlePeopleChange()" required>
                        </div>
                    </div>
                    <div id="participant_section" style="display:none;">
                        <div id="participant_inputs"></div>
                    </div>

                    <!-- 4. ເລືອກບ່ອນນັ່ງ -->
                    <div class="section-title text-dark" style="border-color:#2d3436"><i class="fas fa-couch me-2"></i>4. ເລືອກບ່ອນນັ່ງ</div>
                    <div class="p-4 bg-light rounded-4 border text-center">
                        <div class="d-flex flex-wrap justify-content-center gap-2" id="seatMap" style="max-width: 350px; margin: 0 auto;"></div>
                        <input type="hidden" name="selected_seats" id="selected_seats_input" required>
                    </div>
                </div>
            </div>

            <!-- ເບື້ອງຂວາ: ສະຫຼຸບລາຄາ -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-5 overflow-hidden sticky-top" style="top: 20px;">
                    <img src="assets/uploads/tours/<?php echo $tour['image']; ?>" class="w-100" style="height: 200px; object-fit: cover;">
                    <div class="card-body p-4 text-center">
                        <h5 class="fw-bold"><?php echo $tour['tour_name']; ?></h5>
                        <div class="p-3 bg-light rounded-4 border mt-3">
                            <small class="text-muted fw-bold">ລາຄາລວມທັງໝົດ</small>
                            <h2 class="text-danger fw-bold mb-0" id="display_total"><?php echo number_format($tour['price']); ?></h2>
                            <small class="fw-bold">LAK</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-lg fw-bold py-3 mt-4">ຢືນຢັນການຈອງ</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let selectedSeats = [];

function handlePeopleChange() {
    updateTotal();
    renderSeatMap();
    generateParticipantFields();
}

function generateParticipantFields() {
    const num = parseInt(document.getElementById('num_people').value) || 1;
    const container = document.getElementById('participant_inputs');
    const section = document.getElementById('participant_section');
    container.innerHTML = '';
    if (num > 1) {
        section.style.display = 'block';
        for (let i = 2; i <= num; i++) {
            container.innerHTML += `
                <div class="participant-item shadow-sm border-start border-4 border-primary">
                    <div class="row g-2">
                        <div class="col-md-6"><label class="small fw-bold">ຄົນທີ ${i}: ຊື່ ແລະ ນາມສະກຸນ</label><input type="text" name="participant_names[]" class="form-control form-control-sm" required></div>
                        <div class="col-md-6"><label class="small fw-bold">ບັດປະຈຳໂຕ / ພາສປອດ</label><input type="text" name="participant_id_cards[]" class="form-control form-control-sm" required></div>
                    </div>
                </div>`;
        }
    } else { section.style.display = 'none'; }
}

function renderSeatMap() {
    const mapContainer = document.getElementById('seatMap');
    const tourId = <?php echo $tour_id; ?>;
    const maxSeats = <?php echo $tour['max_seats']; ?>;
    fetch(`get_occupied_seats.php?tour_id=${tourId}`).then(res => res.json()).then(occupied => {
        mapContainer.innerHTML = ''; selectedSeats = [];
        document.getElementById('selected_seats_input').value = '';
        for (let i = 1; i <= maxSeats; i++) {
            const sId = i.toString(); const isOcc = occupied.includes(sId);
            const div = document.createElement('div');
            div.className = `seat ${isOcc ? 'occupied' : ''}`; div.innerText = i;
            if (!isOcc) {
                div.onclick = function() {
                    const limit = parseInt(document.getElementById('num_people').value);
                    if (this.classList.contains('selected')) {
                        this.classList.remove('selected'); selectedSeats = selectedSeats.filter(s => s !== sId);
                    } else {
                        if (selectedSeats.length < limit) { this.classList.add('selected'); selectedSeats.push(sId); }
                        else { Swal.fire({ icon: 'warning', text: `ທ່ານເລືອກໄດ້ພຽງ ${limit} ບ່ອນ` }); }
                    }
                    document.getElementById('selected_seats_input').value = selectedSeats.join(',');
                };
            }
            mapContainer.appendChild(div);
        }
    });
}

function updateTotal() {
    const price = <?php echo $tour['price']; ?>;
    const num = parseInt(document.getElementById('num_people').value) || 1;
    document.getElementById('display_total').innerText = new Intl.NumberFormat().format(price * num);
}

function validateForm() {
    const num = parseInt(document.getElementById('num_people').value);
    if (selectedSeats.length !== num) {
        Swal.fire({ icon: 'error', title: 'ເລືອກບ່ອນນັ່ງ', text: `ກະລຸນາເລືອກບ່ອນນັ່ງໃຫ້ຄົບ ${num} ບ່ອນ` });
        return false;
    }
    return true;
}
window.onload = renderSeatMap;
</script>
</body>
</html>