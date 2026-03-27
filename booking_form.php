<?php include 'config/db.php'; 

if(!isset($_GET['tour_id'])) { header("Location: index.php"); exit(); }

$tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);
$res = mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$tour_id' AND status = 'Active'");
$tour = mysqli_fetch_assoc($res);

if(!$tour) { header("Location: index.php"); exit(); }

// ຄຳນວນບ່ອນນັ່ງ
$booked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tour_id AND status != 'Cancelled'"));
$remaining = $tour['max_seats'] - ($booked['total'] ?? 0);
?>
<!DOCTYPE html>
<html lang="<?php echo ($current_lang == 'lao') ? 'lo' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['form_title']; ?> - <?php echo $tour['tour_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; }
        .summary-card { border-radius: 20px; border: none; position: sticky; top: 100px; }
        .form-card { border-radius: 20px; border: none; }
        .price-total { font-size: 2.2rem; color: #ff4757; font-weight: 700; }
        .participant-row { background: #f8f9fa; border-radius: 15px; padding: 15px; margin-bottom: 12px; border: 1px solid #eee; }
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
        <!-- ເບື້ອງຊ້າຍ: ຟອມກອກຂໍ້ມູນ -->
        <div class="col-lg-7">
            <div class="card form-card shadow-sm p-4 p-md-5 bg-white">
                <h3 class="fw-bold text-primary mb-4"><i class="fas fa-edit me-2"></i><?php echo $lang['form_title']; ?></h3>
                
                <form action="process_booking.php" method="POST">
                    <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                    <input type="hidden" name="price" id="tour_price" value="<?php echo $tour['price']; ?>">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold"><?php echo $lang['form_fullname']; ?></label>
                            <input type="text" name="fullname" class="form-control bg-light border-0 py-2" placeholder="<?php echo ($current_lang=='lao')?'ປ້ອນຊື່ ແລະ ນາມສະກຸນຂອງທ່ານ...':'Enter your full name...'; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?php echo $lang['form_phone']; ?></label>
                            <input type="text" name="phone" class="form-control bg-light border-0 py-2" placeholder="020..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?php echo $lang['form_email']; ?></label>
                            <input type="email" name="email" class="form-control bg-light border-0 py-2" placeholder="example@gmail.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary"><?php echo $lang['form_date']; ?></label>
                            <input type="date" name="travel_date" class="form-control py-2 border-primary" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?php echo $lang['form_pax']; ?></label>
                            <input type="number" name="num_people" id="num_people" class="form-control py-2 fw-bold" value="1" min="1" max="<?php echo $remaining; ?>" oninput="generateParticipants(); updateTotal();" required>
                            <small class="text-muted"><?php echo ($current_lang=='lao')?'ຫວ່າງ':'Available'; ?>: <?php echo $remaining; ?> <?php echo ($current_lang=='lao')?'ບ່ອນ':'Seats'; ?></small>
                        </div>
                    </div>

                    <!-- Participant Names Section -->
                    <div id="participant_section" class="mt-5" style="display:none;">
                        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-users me-2"></i><?php echo ($current_lang=='lao')?'ຂໍ້ມູນຜູ້ຮ່ວມເດີນທາງ':'Participant Details'; ?></h6>
                        <div id="participant_inputs">
                            <!-- JS Will generate fields here -->
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="mt-4">
                        <label class="form-label fw-bold small text-muted"><?php echo $lang['form_note']; ?></label>
                        <textarea name="note" class="form-control bg-light border-0" rows="3" placeholder="<?php echo ($current_lang=='lao')?'ລະບຸທີ່ນີ້...':'Specify here...'; ?>"></textarea>
                    </div>

                    <div class="mt-5 border-top pt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-lg fw-bold py-3">
                            <?php echo $lang['form_btn_submit']; ?> <i class="fas fa-chevron-right ms-2"></i>
                        </button>
                        <a href="index.php" class="btn btn-link w-100 text-muted mt-2 text-decoration-none"><?php echo $lang['form_btn_cancel']; ?></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- ເບື້ອງຂວາ: ສະຫຼຸບແພັກເກັດ (Sticky) -->
        <div class="col-lg-5">
            <div class="card summary-card shadow-lg overflow-hidden bg-white">
                <img src="assets/uploads/tours/<?php echo $tour['image']; ?>" class="w-100" style="height: 200px; object-fit: cover;">
                <div class="card-body p-4">
                    <span class="badge bg-primary mb-2"><?php echo $tour['category']; ?></span>
                    <h4 class="fw-bold text-dark mb-3"><?php echo $tour['tour_name']; ?></h4>
                    <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt text-danger me-2"></i><?php echo ($current_lang=='lao')?'ນັດພົບ':'Meet at'; ?>: <?php echo $tour['meeting_point']; ?></p>
                    
                    <hr>
                    
                    <h6 class="fw-bold text-success small mb-3"><i class="fas fa-check-circle me-2"></i><?php echo ($current_lang=='lao')?'ສິ່ງທີ່ທ່ານຈະໄດ້ຮັບ:':'What you will get:'; ?></h6>
                    <div class="small text-muted mb-4" style="white-space: pre-line; line-height: 1.6;"><?php echo $tour['whats_included']; ?></div>
                    
                    <div class="bg-light p-4 rounded-4 mt-4 text-center border">
                        <span class="text-muted d-block small fw-bold text-uppercase mb-1"><?php echo $lang['form_total']; ?></span>
                        <h1 class="price-total mb-0" id="display_total"><?php echo number_format($tour['price']); ?></h1>
                        <span class="fw-bold text-muted">LAK</span>
                    </div>

                    <div class="mt-4 alert alert-info border-0 small py-2">
                        <i class="fas fa-info-circle me-2"></i> <?php echo ($current_lang=='lao')?'ຫຼັງຈາກກົດຢືນຢັນ ທ່ານຈະສາມາດແນບຫຼັກຖານການໂອນເງິນໄດ້.':'After confirmation, you can attach your payment slip.'; ?>
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
    const lang = '<?php echo $current_lang; ?>';
    
    if (num > max) {
        alert(lang === 'lao' ? "ຂໍອະໄພ! ບ່ອນນັ່ງຫວ່າງບໍ່ພໍ" : "Sorry! Not enough seats available");
        document.getElementById('num_people').value = max;
        generateParticipants();
        return;
    }

    container.innerHTML = '';
    if (num > 1) {
        section.style.display = 'block';
        for (let i = 2; i <= num; i++) {
            container.innerHTML += `
                <div class="participant-row shadow-sm">
                    <div class="row g-2">
                        <div class="col-7">
                            <label class="small fw-bold text-muted">${lang==='lao'?'ຄົນທີ':'Person'} ${i}: ${lang==='lao'?'ຊື່ ແລະ ນາມສະກຸນ':'Full Name'}</label>
                            <input type="text" name="participant_names[]" class="form-control form-control-sm border-0" required>
                        </div>
                        <div class="col-5">
                            <label class="small fw-bold text-muted">${lang==='lao'?'ເບີໂທ':'Phone'}</label>
                            <input type="text" name="participant_phones[]" class="form-control form-control-sm border-0" required>
                        </div>
                    </div>
                </div>`;
        }
    } else {
        section.style.display = 'none';
    }
}

function updateTotal() {
    const price = document.getElementById('tour_price').value;
    const num = document.getElementById('num_people').value || 0;
    const total = price * num;
    document.getElementById('display_total').innerText = new Intl.NumberFormat().format(total);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>