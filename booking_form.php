<?php include 'config/db.php'; 

if(!isset($_GET['tour_id'])) { header("Location: index.php"); exit(); }

$tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);
$res = mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$tour_id' AND status = 'Active'");
$tour = mysqli_fetch_assoc($res);

if(!$tour) { header("Location: index.php"); exit(); }

// ຄຳນວນບ່ອນນັ່ງຫວ່າງ
$booked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tour_id AND status != 'Cancelled'"));
$remaining = $tour['max_seats'] - ($booked['total'] ?? 0);

// ກວດສອບລາຄາ (ກັນເໜືອຄວາມຄາດໝາຍ)
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; }
        .summary-card { border-radius: 25px; border: none; position: sticky; top: 100px; }
        .form-card { border-radius: 25px; border: none; }
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
                    <!-- ໃສ່ລາຄາລົງໃນ hidden input ໃຫ້ເປະ -->
                    <input type="hidden" name="price" id="tour_price" value="<?php echo $base_price; ?>">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small"><?php echo $lang['form_fullname']; ?></label>
                            <input type="text" name="fullname" class="form-control bg-light border-0 py-2" placeholder="ຊື່ ແລະ ນາມສະກຸນ" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small"><?php echo $lang['form_phone']; ?></label>
                            <input type="text" name="phone" id="phone" class="form-control bg-light border-0 py-2" placeholder="020..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small"><?php echo $lang['form_email']; ?></label>
                            <input type="email" name="email" class="form-control bg-light border-0 py-2" placeholder="example@gmail.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary"><?php echo $lang['form_date']; ?></label>
                            <input type="date" name="travel_date" class="form-control py-2 border-primary" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?php echo $lang['form_pax']; ?></label>
                            <input type="number" name="num_people" id="num_people" class="form-control py-2 fw-bold text-primary" value="1" min="1" max="<?php echo $remaining; ?>" oninput="generateParticipants(); updateTotal();" required>
                            <small class="text-muted">ຫວ່າງ: <?php echo $remaining; ?> ບ່ອນ</small>
                        </div>
                    </div>

                    <div id="participant_section" class="mt-5" style="display:none;">
                        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-users me-2"></i>ຂໍ້ມູນຜູ້ຮ່ວມເດີນທາງ</h6>
                        <div id="participant_inputs"></div>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-4 border">
                        <label class="form-label fw-bold small text-primary">ລະຫັດສ່ວນຫຼຸດ (Promo Code)</label>
                        <div class="input-group">
                            <input type="text" id="coupon_code" class="form-control border-0 shadow-none" placeholder="Enter code...">
                            <button type="button" onclick="applyCoupon()" class="btn btn-dark px-4">Apply</button>
                        </div>
                        <div id="coupon_msg" class="small mt-1"></div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label fw-bold small text-muted"><?php echo $lang['form_note']; ?></label>
                        <textarea name="note" class="form-control bg-light border-0" rows="3" placeholder="..."></textarea>
                    </div>

                    <div class="mt-5 border-top pt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-lg fw-bold py-3">
                            <?php echo $lang['form_btn_submit']; ?>
                        </button>
                    </div>

                    <input type="hidden" name="coupon_id" id="coupon_id_input" value="">
                    <input type="hidden" name="discount_val" id="discount_val_input" value="0">
                </form>
            </div>
        </div>

        <!-- ເບື້ອງຂວາ: ສະຫຼຸບລາຄາ -->
        <div class="col-lg-5">
            <div class="card summary-card shadow-lg bg-white overflow-hidden border-0">
                <img src="assets/uploads/tours/<?php echo $tour['image']; ?>" class="w-100" style="height: 200px; object-fit: cover;">
                <div class="card-body p-4 text-center">
                    <h4 class="fw-bold text-dark mb-4"><?php echo $tour['tour_name']; ?></h4>
                    
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span><?php echo ($current_lang=='lao')?'ລາຄາປົກກະຕິ':'Subtotal'; ?>:</span>
                        <!-- ເພີ່ມ id="subtotal_display" ບ່ອນນີ້ -->
                        <span id="subtotal_display"><?php echo number_format($base_price); ?> LAK</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-success fw-bold">
                        <span><?php echo ($current_lang=='lao')?'ສ່ວນຫຼຸດ':'Discount'; ?>:</span>
                        <span id="discount_display">- 0 LAK</span>
                    </div>

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
let currentDiscount = 0;

function generateParticipants() {
    const num = parseInt(document.getElementById('num_people').value) || 1;
    const container = document.getElementById('participant_inputs');
    const section = document.getElementById('participant_section');
    const max = <?php echo $remaining; ?>;
    const lang = '<?php echo $current_lang; ?>';
    
    if (num > max) {
        alert(lang === 'lao' ? "ຂໍອະໄພ! ບ່ອນນັ່ງຫວ່າງບໍ່ພໍ" : "Not enough seats");
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

function applyCoupon() {
    const code = document.getElementById('coupon_code').value;
    const tourId = <?php echo $tour_id; ?>;
    const num = parseInt(document.getElementById('num_people').value) || 1;
    const subtotal = parseFloat(document.getElementById('tour_price').value) * num;
    const phone = document.getElementById('phone').value;
    const msg = document.getElementById('coupon_msg');

    if(!code || !phone) { alert("Please enter phone and code"); return; }

    fetch('check_coupon.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `code=${encodeURIComponent(code)}&tour_id=${tourId}&subtotal=${subtotal}&phone=${phone}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            currentDiscount = data.discount;
            document.getElementById('coupon_id_input').value = data.id;
            document.getElementById('discount_val_input').value = currentDiscount;
            msg.innerHTML = `<span class="text-success small"><i class="fas fa-check"></i> Applied -${new Intl.NumberFormat().format(currentDiscount)} LAK</span>`;
        } else {
            currentDiscount = 0;
            document.getElementById('coupon_id_input').value = '';
            document.getElementById('discount_val_input').value = 0;
            msg.innerHTML = `<span class="text-danger small">${data.message}</span>`;
        }
        updateTotal();
    });
}

function updateTotal() {
    const price = parseFloat(document.getElementById('tour_price').value) || 0;
    const num = parseInt(document.getElementById('num_people').value) || 1;
    const subtotal = price * num;
    const total = subtotal - currentDiscount;
    
    document.getElementById('subtotal_display').innerText = new Intl.NumberFormat().format(subtotal) + " LAK";
    document.getElementById('discount_display').innerText = "- " + new Intl.NumberFormat().format(currentDiscount) + " LAK";
    document.getElementById('display_total').innerText = new Intl.NumberFormat().format(total > 0 ? total : 0);
}

// ບັງຄັບໃຫ້ຄຳນວນລາຄາທັນທີທີ່ເປີດໜ້າ
window.onload = function() {
    updateTotal();
    generateParticipants();
};
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>