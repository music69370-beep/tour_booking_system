<?php include 'config/db.php'; 
/** @var mysqli $conn */
/** @var array $lang */
if(!isset($_GET['tour_id'])) { header("Location: index.php"); exit(); }

$tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);

// ດຶງຂໍ້ມູນທົວ
$res = mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$tour_id' AND status = 'Active'");
$tour = mysqli_fetch_assoc($res);

if(!$tour) { header("Location: index.php"); exit(); }

// ຄຳນວນບ່ອນນັ່ງຫວ່າງ
$booked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tour_id AND status != 'Cancelled'"));
$remaining = $tour['max_seats'] - ($booked['total'] ?? 0);

// ລາຄາພື້ນຖານຈາກຖານຂໍ້ມູນ
$base_price = (float)$tour['price']; 
?>
<!DOCTYPE html>
<html lang="<?php echo ($current_lang == 'lao') ? 'lo' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['form_title']; ?> - <?php echo $tour['tour_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; }
        .form-card { border-radius: 25px; border: none; }
        .price-total { font-size: 2.2rem; color: #ff4757; font-weight: 700; }
        
        /* Seat Map Styles */
        .seat-map-wrapper { background: #fff; padding: 25px; border-radius: 20px; border: 2px dashed #cbd5e0; }
        .van-grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 12px; 
            max-width: 220px; 
            margin: 0 auto; 
        }
        .seat { 
            width: 55px; height: 55px; background: white; border: 2px solid #cbd5e0; 
            border-radius: 12px; display: flex; align-items: center; justify-content: center; 
            cursor: pointer; font-weight: bold; font-size: 14px; transition: 0.2s;
        }
        .seat.selected { background: #0d6efd; color: white; border-color: #0d6efd; transform: scale(1.05); }
        .seat.occupied { background: #ff4757; color: white; cursor: not-allowed; border-color: #ff4757; opacity: 0.8; }
        .seat:hover:not(.occupied) { border-color: #0d6efd; color: #0d6efd; }
    </style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php"><i class="fas fa-arrow-left me-2"></i> <?php echo ($current_lang=='lao')?'ກັບໄປໜ້າຫຼັກ':'Back to Home'; ?></a>
    </div>
</nav>

<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card form-card shadow-sm p-4 p-md-5 bg-white">
                <h3 class="fw-bold text-primary mb-1"><i class="fas fa-edit me-2"></i><?php echo $lang['form_title']; ?></h3>
                <p class="text-muted mb-4 small">ກະລຸນາປ້ອນຂໍ້ມູນ ແລະ ເລືອກບ່ອນນັ່ງໃຫ້ຄົບຖ້ວນ</p>
                
                <form action="process_booking.php" method="POST" onsubmit="return validateForm()">
                    <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                    <input type="hidden" name="price" id="tour_price" value="<?php echo $base_price; ?>">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small"><?php echo $lang['form_fullname']; ?></label>
                            <input type="text" name="fullname" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small"><?php echo $lang['form_phone']; ?></label>
                            <input type="text" name="phone" id="phone" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small"><?php echo $lang['form_email']; ?></label>
                            <input type="email" name="email" class="form-control bg-light border-0 py-2" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger"><?php echo $lang['form_date']; ?></label>
                            <input type="text" class="form-control bg-white border-danger fw-bold text-danger" value="<?php echo date('d/m/Y', strtotime($tour['start_date'])); ?>" readonly>
                            <input type="hidden" name="travel_date" value="<?php echo $tour['start_date']; ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">ຈຳນວນຄົນ</label>
                            <input type="number" name="num_people" id="num_people" class="form-control py-2 fw-bold text-primary border-primary" value="1" min="1" max="<?php echo $remaining; ?>" onchange="renderSeatMap(); updateTotal();" required>
                        </div>
                    </div>

                    <!-- ເລືອກບ່ອນນັ່ງ -->
                    <div class="mt-5 seat-map-wrapper">
                        <h6 class="fw-bold text-center mb-4 text-dark"><i class="fas fa-couch me-2"></i>ເລືອກບ່ອນນັ່ງຂອງທ່ານ (ລົດຕູ້)</h6>
                        <div class="van-grid" id="seatMap"></div>
                        <input type="hidden" name="selected_seats" id="selected_seats_input" required>

                        <div class="d-flex justify-content-center gap-3 mt-4 small fw-bold">
                            <span><span class="badge border text-dark bg-white"> </span> ຫວ່າງ</span>
                            <span><span class="badge bg-primary"> </span> ເລືອກ</span>
                            <span><span class="badge bg-danger"> </span> ເຕັມ</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label fw-bold small text-muted"><?php echo $lang['form_note']; ?></label>
                        <textarea name="note" class="form-control bg-light border-0" rows="2"></textarea>
                    </div>

                    <div class="mt-5 border-top pt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-lg fw-bold py-3">
                            <?php echo $lang['form_btn_submit']; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-lg bg-white overflow-hidden border-0 sticky-top" style="top: 100px;">
                <img src="assets/uploads/tours/<?php echo $tour['image']; ?>" class="w-100" style="height: 200px; object-fit: cover;">
                <div class="card-body p-4 text-center">
                    <h4 class="fw-bold text-dark mb-4"><?php echo $tour['tour_name']; ?></h4>
                    <div class="bg-light p-4 rounded-4 border">
                        <span class="text-muted d-block small fw-bold text-uppercase mb-1"><?php echo $lang['form_total']; ?></span>
                        <h1 class="price-total mb-0" id="display_total"><?php echo number_format($base_price); ?></h1>
                        <span class="fw-bold text-muted small">LAK</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedSeats = [];

function renderSeatMap() {
    const mapContainer = document.getElementById('seatMap');
    const tourId = <?php echo $tour_id; ?>;
    const maxSeats = <?php echo $tour['max_seats']; ?>;
    
    fetch(`get_occupied_seats.php?tour_id=${tourId}`)
    .then(res => res.json())
    .then(occupied => {
        mapContainer.innerHTML = '';
        selectedSeats = [];
        document.getElementById('selected_seats_input').value = '';

        for (let i = 1; i <= maxSeats; i++) {
            const seatId = i.toString();
            const isFull = occupied.includes(seatId);
            const div = document.createElement('div');
            div.className = `seat ${isFull ? 'occupied' : ''}`;
            div.innerText = i;

            if (!isFull) {
                div.onclick = function() {
                    const limit = parseInt(document.getElementById('num_people').value);
                    if (this.classList.contains('selected')) {
                        this.classList.remove('selected');
                        selectedSeats = selectedSeats.filter(s => s !== seatId);
                    } else {
                        if (selectedSeats.length < limit) {
                            this.classList.add('selected');
                            selectedSeats.push(seatId);
                        } else {
                            Swal.fire({ icon: 'warning', title: 'ເຕັມແລ້ວ', text: `ທ່ານເລືອກໄດ້ພຽງ ${limit} ບ່ອນນັ່ງ` });
                        }
                    }
                    document.getElementById('selected_seats_input').value = selectedSeats.join(',');
                };
            }
            mapContainer.appendChild(div);
        }
    });
}

function validateForm() {
    const num = parseInt(document.getElementById('num_people').value);
    if (selectedSeats.length !== num) {
        Swal.fire({ icon: 'error', title: 'ກະລຸນາເລືອກບ່ອນນັ່ງ', text: `ທ່ານຕ້ອງເລືອກບ່ອນນັ່ງໃຫ້ຄົບ ${num} ບ່ອນ` });
        return false;
    }
    return true;
}

function updateTotal() {
    const price = parseFloat(document.getElementById('tour_price').value);
    const num = parseInt(document.getElementById('num_people').value) || 1;
    document.getElementById('display_total').innerText = new Intl.NumberFormat().format(price * num);
}

window.onload = renderSeatMap;
</script>
</body>
</html>