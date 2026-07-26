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
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap');
        
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #1cc88a;
            --danger-soft: #fff5f5;
            --blue-soft: #eef2ff;
        }

        body { 
            font-family: 'Noto Sans Lao', sans-serif; 
            background-color: #f0f2f5; 
            color: #4a4a4a;
        }

        .form-card { 
            border-radius: 30px; 
            border: none; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
        }

        .section-title { 
            font-weight: 700; 
            color: #333; 
            font-size: 1.2rem;
            margin-bottom: 0;
        }

        /* Input Styling */
        .form-control, .form-select {
            border-radius: 15px;
            padding: 12px 20px;
            border: 1px solid #e3e6f0;
            background-color: #fcfcfc;
            transition: 0.3s;
        }
        .form-control:focus {
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(78, 115, 223, 0.1);
            border-color: var(--primary-color);
        }

        /* Seat Styling */
        .seat-container {
            background: #f8f9fc;
            padding: 30px;
            border-radius: 25px;
            border: 1px dashed #d1d3e2;
        }

        .seat { 
            width: 50px; 
            height: 50px; 
            background: white; 
            border: 2px solid #d1d3e2; 
            border-radius: 15px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            cursor: pointer; 
            font-weight: 700; 
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 1rem;
            color: #858796;
        }
        .seat:hover:not(.occupied) {
            border-color: var(--primary-color);
            transform: translateY(-3px);
            color: var(--primary-color);
        }
        .seat.selected { 
            background: var(--primary-color) !important; 
            color: white !important; 
            border-color: var(--primary-color) !important;
            box-shadow: 0 8px 15px rgba(78, 115, 223, 0.4);
        }
        .seat.occupied { 
            background: #eaecf4 !important; 
            color: #b7b9cc !important; 
            cursor: not-allowed !important; 
            border-color: #eaecf4 !important;
            opacity: 0.7;
        }

        /* Room Selection Card */
        .room-card-input { 
            cursor: pointer; 
            border: 2px solid #f1f3f7; 
            border-radius: 20px; 
            transition: 0.3s; 
            background: white;
            padding: 20px !important;
        }
        .room-check:checked + .room-card-input { 
            border-color: var(--primary-color); 
            background-color: var(--blue-soft); 
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.1); 
        }

        /* Participant Item */
        .participant-item { 
            background: white; 
            border-radius: 20px; 
            padding: 20px; 
            border: 1px solid #eee; 
            margin-bottom: 15px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        /* Sticky Summary */
        .summary-card {
            border-radius: 30px;
            border: none;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .price-box {
            background: var(--danger-soft);
            padding: 25px;
            border-radius: 20px;
            margin: 20px 0;
            border: 2px dashed #ff8d8d;
        }

        .btn-confirm {
            padding: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
            transition: 0.3s;
        }
        .btn-confirm:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(13, 110, 253, 0.3);
        }
    </style>
</head>
<body>

<div class="container my-5">
    <form action="process_booking.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
        <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
        <input type="hidden" name="price" id="base_price" value="<?php echo $tour['price']; ?>">

        <div class="row g-4">
            <!-- Left Side: Form -->
            <div class="col-lg-8">
                <div class="card form-card p-4 p-md-5 bg-white">
                    <div class="d-flex align-items-center mb-5">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-file-signature fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-0">ຢືນຢັນການຈອງທົວຂອງທ່ານ</h2>
                    </div>
                    
                    <!-- Section 1 -->
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-user"></i></div>
                        <h4 class="section-title">1. ຂໍ້ມູນຜູ້ຈອງຫຼັກ (ຫົວໜ້າຄະນະ)</h4>
                    </div>
                    
                    <div class="row g-4 mb-5">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted">ຊື່ ແລະ ນາມສະກຸນ</label>
                            <input type="text" name="fullname" class="form-control" placeholder="ປ້ອນຊື່ແທ້..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">ເພດ</label>
                            <select name="gender" class="form-select">
                                <option value="Male">ຊາຍ (Male)</option>
                                <option value="Female">ຍິງ (Female)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">ວັນເດືອນປີເກີດ</label>
                            <input type="date" name="birthday" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">ສັນຊາດ</label>
                            <input type="text" name="nationality" class="form-control" value="Lao" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">ເລກບັດປະຈຳຕົວ / ພາສປອດ</label>
                            <input type="text" name="id_card_no" class="form-control" placeholder="ປ້ອນເລກບັດ..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary">ຮູບພາບບັດ (ອັບໂຫລດ)</label>
                            <input type="file" name="id_card_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">ເບີໂທລະສັບ (WhatsApp)</label>
                            <input type="text" name="phone" class="form-control" placeholder="020..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">ອີເມວ</label>
                            <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                        </div>
                    </div>

                    <!-- Section 2 -->
                    <div class="section-header">
                        <div class="section-icon" style="background: #ff4757;"><i class="fas fa-phone-alt"></i></div>
                        <h4 class="section-title">2. ຂໍ້ມູນຕິດຕໍ່ສຸກເສີນ</h4>
                    </div>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">ຊື່ຜູ້ຕິດຕໍ່ສຸກເສີນ</label>
                            <input type="text" name="emergency_name" class="form-control" placeholder="ຊື່ຍາດພີ່ນ້ອງ..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">ເບີໂທສຸກເສີນ</label>
                            <input type="text" name="emergency_phone" class="form-control" placeholder="ເບີໂທ..." required>
                        </div>
                    </div>

                    <!-- Section 3 -->
                    <div class="section-header">
                        <div class="section-icon" style="background: #f1c40f;"><i class="fas fa-bed"></i></div>
                        <h4 class="section-title">3. ປະເພດຫ້ອງພັກ</h4>
                    </div>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <input type="radio" name="room_type" value="Twin" id="roomTwin" class="d-none room-check" checked onchange="updateTotal()">
                            <label for="roomTwin" class="room-card-input d-block">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold fs-5"><i class="fas fa-users me-2"></i>Twin Sharing</span>
                                    <span class="badge bg-success">FREE</span>
                                </div>
                                <small class="text-muted">ພັກຫ້ອງຄູ່ (ນອນນຳໝູ່ໃນທີມ)</small>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="radio" name="room_type" value="Single" id="roomSingle" class="d-none room-check" onchange="updateTotal()">
                            <label for="roomSingle" class="room-card-input d-block">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold fs-5 text-danger"><i class="fas fa-user me-2"></i>Single Room</span>
                                    <span class="fw-bold text-danger">+ 200,000 ກີບ</span>
                                </div>
                                <small class="text-muted">ນອນຫ້ອງດ່ຽວສ່ວນຕົວ</small>
                            </label>
                        </div>
                    </div>

                    <!-- Section 4 -->
                    <div class="section-header">
                        <div class="section-icon" style="background: #1cc88a;"><i class="fas fa-users"></i></div>
                        <h4 class="section-title">4. ຈຳນວນຄົນ ແລະ ຜູ້ຮ່ວມທາງ</h4>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ຈຳນວນຄົນທັງໝົດ</label>
                            <input type="number" name="num_people" id="num_people" class="form-control border-success fw-bold fs-5" value="1" min="1" max="<?php echo $remaining; ?>" oninput="handlePeopleChange()" required>
                        </div>
                    </div>
                    <div id="participant_section" style="display:none;">
                        <div id="participant_inputs"></div>
                    </div>

                    <!-- Section 5 -->
                    <div class="section-header">
                        <div class="section-icon" style="background: #34495e;"><i class="fas fa-couch"></i></div>
                        <h4 class="section-title">5. ເລືອກບ່ອນນັ່ງທີ່ທ່ານມັກ</h4>
                    </div>
                    <div class="seat-container text-center">
                        <div class="mb-4 d-flex justify-content-center gap-4 small fw-bold">
                            <div class="d-flex align-items-center"><span class="seat me-2" style="width:20px;height:20px;"></span> ວ່າງ</div>
                            <div class="d-flex align-items-center"><span class="seat selected me-2" style="width:20px;height:20px;"></span> ທີ່ເລືອກ</div>
                            <div class="d-flex align-items-center"><span class="seat occupied me-2" style="width:20px;height:20px;"></span> ເຕັມແລ້ວ</div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-center gap-3" id="seatMap" style="max-width: 450px; margin: 0 auto;"></div>
                        <input type="hidden" name="selected_seats" id="selected_seats_input" required>
                    </div>
                </div>
            </div>

            <!-- Right Side: Summary Sidebar -->
            <div class="col-lg-4">
                <div class="card summary-card sticky-top" style="top: 20px;">
                    <div style="height: 200px; overflow: hidden;">
                        <img src="assets/uploads/tours/<?php echo $tour['image']; ?>" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-center mb-1"><?php echo $tour['tour_name']; ?></h4>
                        <p class="text-muted small text-center mb-4"><i class="fas fa-map-marker-alt me-1"></i> <?php echo $tour['duration']; ?></p>
                        
                        <div class="price-box text-center">
                            <small class="text-muted fw-bold text-uppercase">ຍອດລວມທີ່ຕ້ອງຊຳລະ</small>
                            <h1 class="text-danger fw-bold mb-0 mt-1" id="display_total">0</h1>
                            <small class="fw-bold text-danger">LAK / ກີບ</small>
                        </div>

                        <div class="bg-light p-3 rounded-4 mb-4">
                            <div class="d-flex justify-content-between small mb-2">
                                <span>ຈຳນວນຄົນ:</span>
                                <span class="fw-bold" id="sum_pax">1 ທ່ານ</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span>ບ່ອນນັ່ງ:</span>
                                <span class="fw-bold text-primary" id="sum_seats">-</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-confirm w-100 btn-lg">
                            ຢືນຢັນການຈອງເລີຍ <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let selectedSeats = [];

function handlePeopleChange() {
    const num = parseInt(document.getElementById('num_people').value) || 1;
    document.getElementById('sum_pax').innerText = num + ' ທ່ານ';
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
                <div class="participant-item shadow-sm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted">ຄົນທີ ${i}: ຊື່ ແລະ ນາມສະກຸນ</label>
                            <input type="text" name="participant_names[]" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <!-- ປ່ຽນບ່ອນນີ້ -->
                            <label class="small fw-bold text-muted">ເບີໂທລະສັບ</label>
                            <input type="text" name="participant_phones[]" class="form-control" placeholder="020..." required>
                        </div>
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
        mapContainer.innerHTML = ''; 
        selectedSeats = [];
        document.getElementById('selected_seats_input').value = '';
        document.getElementById('sum_seats').innerText = '-';

        for (let i = 1; i <= maxSeats; i++) {
            const sId = i.toString(); 
            const isOcc = occupied.includes(sId);
            const div = document.createElement('div');
            div.className = `seat ${isOcc ? 'occupied' : ''}`; 
            div.innerText = i;
            
            if (!isOcc) {
                div.onclick = function() {
                    const limit = parseInt(document.getElementById('num_people').value);
                    if (this.classList.contains('selected')) {
                        this.classList.remove('selected'); 
                        selectedSeats = selectedSeats.filter(s => s !== sId);
                    } else {
                        if (selectedSeats.length < limit) { 
                            this.classList.add('selected'); 
                            selectedSeats.push(sId); 
                        } else { 
                            Swal.fire({ icon: 'warning', text: `ທ່ານເລືອກໄດ້ພຽງ ${limit} ບ່ອນ` }); 
                        }
                    }
                    document.getElementById('selected_seats_input').value = selectedSeats.join(',');
                    document.getElementById('sum_seats').innerText = selectedSeats.join(', ') || '-';
                };
            }
            mapContainer.appendChild(div);
        }
    });
}

function updateTotal() {
    const price = parseFloat(document.getElementById('base_price').value);
    const num = parseInt(document.getElementById('num_people').value) || 1;
    const roomType = document.querySelector('input[name="room_type"]:checked').value;
    let supplement = (roomType === 'Single') ? 200000 : 0;
    let total = (price * num) + supplement;
    document.getElementById('display_total').innerText = new Intl.NumberFormat().format(total);
}

function validateForm() {
    const num = parseInt(document.getElementById('num_people').value);
    if (selectedSeats.length !== num) {
        Swal.fire({ icon: 'error', title: 'ເລືອກບ່ອນນັ່ງ', text: `ກະລຸນາເລືອກບ່ອນນັ່ງໃຫ້ຄົບ ${num} ບ່ອນ` });
        return false;
    }
    return true;
}

window.onload = function() {
    renderSeatMap();
    updateTotal();
};
</script>
</body>
</html>