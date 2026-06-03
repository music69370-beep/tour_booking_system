<?php 
include '../../config/db.php';
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>ສ້າງການຈອງທົວ (Fixed Date)</h2>
        </div>

        <form action="save.php" method="POST" id="bookingForm">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <div class="row g-4">
                            <!-- ເລືອກແພັກເກັດ -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ເລືອກແພັກເກັດທົວ</label>
                                <select name="tour_id" id="tour_id" class="form-select select2" onchange="updateTourInfo()" required>
                                    <option value="" data-price="0" data-remain="0" data-start="">-- ຄົ້ນຫາແພັກເກັດ --</option>
                                    <?php 
                                    $res_t = mysqli_query($conn, "SELECT t.* FROM tours t WHERE t.status='Active'");
                                    while($t = mysqli_fetch_assoc($res_t)) {
                                        $tid = $t['tour_id'];
                                        $booked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
                                        $remain = $t['max_seats'] - ($booked['total'] ?? 0);
                                        echo "<option value='".$t['tour_id']."' data-price='".$t['price']."' data-remain='".$remain."' data-start='".$t['start_date']."'>".$t['tour_name']." (ເດີນທາງ: ".date('d/m/Y', strtotime($t['start_date'])).")</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-danger">ວັນທີເດີນທາງ</label>
                                <input type="date" name="travel_date" id="travel_date" class="form-control bg-light border-0 fw-bold text-danger" readonly required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ເລືອກລູກຄ້າ</label>
                                <select name="customer_id" id="customer_id" class="form-select select2" onchange="resetCoupon()" required>
                                    <option value="" data-phone="">-- ຄົ້ນຫາລູກຄ້າ --</option>
                                    <?php 
                                    $c_res = mysqli_query($conn, "SELECT customer_id, fullname, phone FROM customers");
                                    while($c = mysqli_fetch_assoc($c_res)) echo "<option value='".$c['customer_id']."' data-phone='".$c['phone']."'>".$c['fullname']." (".$c['phone'].")</option>";
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຈຳນວນຄົນ</label>
                                <input type="number" name="num_people" id="num_people" class="form-control bg-light border-0 py-2" value="1" min="1" oninput="generateParticipants(); updateTotal();" required>
                            </div>
                        </div>

                        <div id="participant_section" class="mt-4" style="display:none;">
                            <h6 class="fw-bold text-primary mb-3">ລາຍຊື່ຜູ້ຮ່ວມທາງ</h6>
                            <div id="participant_inputs"></div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label fw-bold small text-muted">ໝາຍເຫດ</label>
                            <textarea name="note" class="form-control bg-light border-0" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Summary -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 90px;">
                        <h5 class="fw-bold mb-4 border-bottom pb-2">ສະຫຼຸບຍອດເງິນ</h5>
                        
                        <!-- ສ່ວນຂອງ Coupon -->
                        <div class="mb-4 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-bold small text-primary">ລະຫັດສ່ວນຫຼຸດ (Promo Code)</label>
                            <div class="input-group">
                                <input type="text" id="coupon_code" class="form-control border-0 shadow-none" placeholder="ປ້ອນລະຫັດ...">
                                <button type="button" onclick="applyCoupon()" class="btn btn-dark">ໃຊ້</button>
                            </div>
                            <div id="coupon_msg" class="small mt-1"></div>
                        </div>

                        <div class="p-4 rounded-4 text-center mb-4" style="background-color: #fff5f6; border: 2px dashed #ff4757;">
                            <h1 class="text-danger fw-bold mb-0" id="display_total">0</h1>
                            <small class="fw-bold text-danger">LAK</small>
                            
                            <div id="discount_info" class="text-success small mt-2 fw-bold" style="display:none;">
                                ສ່ວນຫຼຸດ: -<span id="display_discount">0</span>
                            </div>

                            <!-- Hidden Fields -->
                            <input type="hidden" name="total_price" id="total_price_val">
                            <input type="hidden" name="coupon_id" id="coupon_id_input" value="">
                            <input type="hidden" name="discount_amount" id="discount_amount_input" value="0">
                        </div>

                        <button type="submit" name="save_booking" class="btn btn-primary btn-lg w-100 rounded-pill shadow fw-bold py-3">ຢືນຢັນການຈອງ</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() { $('.select2').select2({ width: '100%' }); });

let currentDiscount = 0;

function updateTourInfo() {
    const select = document.getElementById('tour_id');
    const selected = select.options[select.selectedIndex];
    document.getElementById('travel_date').value = selected.getAttribute('data-start');
    resetCoupon();
    updateTotal();
    generateParticipants();
}

function resetCoupon() {
    currentDiscount = 0;
    document.getElementById('coupon_id_input').value = '';
    document.getElementById('discount_amount_input').value = 0;
    document.getElementById('coupon_code').value = '';
    document.getElementById('coupon_msg').innerHTML = '';
    document.getElementById('discount_info').style.display = 'none';
}

function applyCoupon() {
    const code = document.getElementById('coupon_code').value;
    const tourId = document.getElementById('tour_id').value;
    const num = parseInt(document.getElementById('num_people').value) || 1;
    const custSelect = document.getElementById('customer_id');
    const phone = custSelect.options[custSelect.selectedIndex].getAttribute('data-phone');
    
    if(!tourId || !phone) { alert("ກະລຸນາເລືອກແພັກເກັດ ແລະ ລູກຄ້າກ່ອນ"); return; }
    if(!code) return;

    const price = parseFloat(document.getElementById('tour_id').options[document.getElementById('tour_id').selectedIndex].getAttribute('data-price')) || 0;
    const subtotal = price * num;

    fetch('../../check_coupon.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `code=${encodeURIComponent(code)}&tour_id=${tourId}&subtotal=${subtotal}&phone=${phone}`
    })
    .then(res => res.json())
    .then(data => {
        const msg = document.getElementById('coupon_msg');
        if(data.status === 'success') {
            currentDiscount = data.discount;
            document.getElementById('coupon_id_input').value = data.id;
            document.getElementById('discount_amount_input').value = currentDiscount;
            document.getElementById('display_discount').innerText = new Intl.NumberFormat().format(currentDiscount);
            document.getElementById('discount_info').style.display = 'block';
            msg.innerHTML = `<span class="text-success">ໃຊ້ລະຫັດສຳເລັດ!</span>`;
        } else {
            currentDiscount = 0;
            document.getElementById('coupon_id_input').value = '';
            document.getElementById('discount_amount_input').value = 0;
            document.getElementById('discount_info').style.display = 'none';
            msg.innerHTML = `<span class="text-danger">${data.message}</span>`;
        }
        updateTotal();
    });
}

function generateParticipants() {
    const num = parseInt(document.getElementById('num_people').value) || 1;
    const container = document.getElementById('participant_inputs');
    const section = document.getElementById('participant_section');
    container.innerHTML = '';
    if (num > 1) {
        section.style.display = 'block';
        for (let i = 2; i <= num; i++) {
            container.innerHTML += `<div class="row g-2 mb-2">
                <div class="col-7"><input type="text" name="participant_names[]" class="form-control form-control-sm bg-light" placeholder="ຊື່ຄົນທີ ${i}" required></div>
                <div class="col-5"><input type="text" name="participant_phones[]" class="form-control form-control-sm bg-light" placeholder="ເບີໂທ" required></div>
            </div>`;
        }
    } else { section.style.display = 'none'; }
}

function updateTotal() {
    const select = document.getElementById('tour_id');
    const selected = select.options[select.selectedIndex];
    const price = parseFloat(selected.getAttribute('data-price')) || 0;
    const num = parseInt(document.getElementById('num_people').value) || 1;
    const subtotal = price * num;
    const total = subtotal - currentDiscount;
    
    document.getElementById('display_total').innerText = new Intl.NumberFormat().format(total > 0 ? total : 0);
    document.getElementById('total_price_val').value = total > 0 ? total : 0;
}
</script>
<?php include '../../includes/footer.php'; ?>