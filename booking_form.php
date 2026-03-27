<?php include 'config/db.php'; 
if(!isset($_GET['tour_id'])) { header("Location: index.php"); exit(); }
$tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);
$tour = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$tour_id' AND status = 'Active'"));
if(!$tour) { header("Location: index.php"); exit(); }

$booked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tour_id AND status != 'Cancelled'"));
$remaining = $tour['max_seats'] - ($booked['total'] ?? 0);
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['form_title']; ?> - <?php echo $tour['tour_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; }
        .summary-card { border-radius: 20px; position: sticky; top: 20px; }
        .price-total { font-size: 2.2rem; color: #ff4757; font-weight: 700; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row g-4">
        <!-- ເບື້ອງຊ້າຍ: ຟອມ -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white">
                <h3 class="fw-bold text-primary mb-4"><?php echo $lang['form_title']; ?></h3>
                <form action="process_booking.php" method="POST">
                    <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                    <input type="hidden" name="price" id="tour_price" value="<?php echo $tour['price']; ?>">

                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-bold small"><?php echo $lang['form_fullname']; ?></label><input type="text" name="fullname" class="form-control bg-light border-0" required></div>
                        <div class="col-md-6"><label class="form-label fw-bold small"><?php echo $lang['form_phone']; ?></label><input type="text" name="phone" class="form-control bg-light border-0" required></div>
                        <div class="col-md-12"><label class="form-label fw-bold small"><?php echo $lang['form_email']; ?></label><input type="email" name="email" class="form-control bg-light border-0" required></div>
                        <div class="col-md-6"><label class="form-label fw-bold small"><?php echo $lang['form_date']; ?></label><input type="date" name="travel_date" class="form-control border-primary" required></div>
                        <div class="col-md-6"><label class="form-label fw-bold small"><?php echo $lang['form_pax']; ?></label><input type="number" name="num_people" id="num_people" class="form-control fw-bold" value="1" min="1" max="<?php echo $remaining; ?>" oninput="updateTotal();" required></div>
                    </div>

                    <!-- Promo Code Section -->
                    <div class="mt-4 p-3 bg-light rounded-4 border">
                        <label class="form-label fw-bold small text-primary">ລະຫັດສ່ວນຫຼຸດ (Promo Code)</label>
                        <div class="input-group">
                            <input type="text" id="coupon_code" class="form-control border-0" placeholder="ກອກລະຫັດທີ່ນີ້...">
                            <button type="button" onclick="applyCoupon()" class="btn btn-dark px-4">ກວດສອບ</button>
                        </div>
                        <div id="coupon_msg" class="small mt-1"></div>
                    </div>

                    <div class="mt-5 border-top pt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-lg fw-bold py-3">ຢືນຢັນການຈອງ</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ເບື້ອງຂວາ: ສະຫຼຸບ -->
        <div class="col-lg-5">
            <div class="card summary-card shadow bg-white border-0 overflow-hidden">
                <img src="assets/uploads/tours/<?php echo $tour['image']; ?>" class="w-100" style="height: 200px; object-fit: cover;">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3"><?php echo $tour['tour_name']; ?></h4>
                    <hr>
                    <div class="d-flex justify-content-between mb-2"><span>ລາຄາປົກກະຕິ:</span><span id="subtotal_display">0</span></div>
                    <div class="d-flex justify-content-between mb-2 text-success fw-bold"><span>ສ່ວນຫຼຸດ:</span><span id="discount_display">0</span></div>
                    <div class="bg-light p-3 rounded-4 mt-3 text-center border">
                        <span class="text-muted d-block small fw-bold">ລາຄາລວມທັງໝົດ</span>
                        <h1 class="price-total mb-0" id="display_total">0</h1>
                        <input type="hidden" name="coupon_id" id="coupon_id_input">
                        <input type="hidden" name="discount_val" id="discount_val_input" value="0">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentDiscount = 0;

function applyCoupon() {
    const code = document.getElementById('coupon_code').value;
    const tourId = <?php echo $tour_id; ?>;
    const subtotal = parseFloat(document.getElementById('tour_price').value) * parseInt(document.getElementById('num_people').value);
    const phone = document.getElementsByName('phone')[0].value;

    if(!phone) { alert("ກະລຸນາກອກເບີໂທກ່ອນໃຊ້ລະຫັດສ່ວນຫຼຸດ"); return; }

    fetch('check_coupon.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `code=${code}&tour_id=${tourId}&subtotal=${subtotal}&phone=${phone}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            currentDiscount = data.discount;
            document.getElementById('coupon_id_input').value = data.id;
            document.getElementById('discount_val_input').value = currentDiscount;
            document.getElementById('coupon_msg').innerHTML = `<span class="text-success">${data.discount.toLocaleString()} ກີບ ຖືກຫັກອອກແລ້ວ!</span>`;
            updateTotal();
        } else {
            alert(data.message);
            // reset logic...
        }
    });
}

function updateTotal() {
    const price = parseFloat(document.getElementById('tour_price').value);
    const num = parseInt(document.getElementById('num_people').value) || 0;
    const subtotal = price * num;
    const total = subtotal - currentDiscount;
    document.getElementById('subtotal_display').innerText = new Intl.NumberFormat().format(subtotal) + " ກີບ";
    document.getElementById('discount_display').innerText = "- " + new Intl.NumberFormat().format(currentDiscount) + " ກີບ";
    document.getElementById('display_total').innerText = new Intl.NumberFormat().format(total > 0 ? total : 0);
}
window.onload = updateTotal;
</script>
</body>
</html>