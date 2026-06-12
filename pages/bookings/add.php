<?php 
include '../../config/db.php';
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    :root { --primary-soft: #e7f1ff; --danger-soft: #fff5f6; --success-soft: #eefaf4; }
    .main-content { background-color: #f4f7f6; }
    .booking-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
    .section-title { font-size: 0.9rem; font-weight: 700; color: #4e73df; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px; display: flex; align-items: center; }
    .section-title i { margin-right: 10px; background: var(--primary-soft); padding: 8px; border-radius: 10px; }
    
    .form-control, .form-select { border-radius: 12px; padding: 12px 15px; border: 1px solid #d1d3e2; background-color: #f8f9fc; }
    .form-control:focus { background-color: #fff; border-color: #4e73df; box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25); }

    /* Seat Map Styling - ປັບປຸງໃຫ້ຮອງຮັບລົດໃຫຍ່ 35 ບ່ອນ */
    .seat-map-wrapper { background: #fff; padding: 30px; border-radius: 25px; border: 1px solid #e3e6f0; min-height: 200px; }
    .bus-head { width: 120px; height: 35px; background: #edeff2; margin: 0 auto 30px; border-radius: 15px 15px 5px 5px; position: relative; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; color: #adb5bd; }
    
    .seat-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(50px, 1fr)); /* ປັບຖັນອັດຕະໂນມັດ */
        gap: 12px; 
        max-width: 450px; /* ຂະຫຍາຍໃຫ້ກວ້າງຂຶ້ນສຳລັບລົດບັດ */
        margin: 0 auto; 
    }
    .seat { 
        width: 50px; height: 50px; background: #f1f3f7; border: 2px solid #e3e6f0; 
        border-radius: 12px; display: flex; align-items: center; justify-content: center; 
        cursor: pointer; font-weight: 700; transition: 0.2s; 
    }
    .seat.selected { background: #4e73df !important; color: white !important; border-color: #2e59d9 !important; transform: scale(1.1); box-shadow: 0 5px 15px rgba(78,115,223,0.3); }
    .seat.occupied { background: #ff4757 !important; color: white !important; cursor: not-allowed; opacity: 0.6; border-color: #e02d3d; }
    .seat:hover:not(.occupied):not(.selected) { border-color: #4e73df; background: #fff; }

    .summary-card { position: sticky; top: 90px; border: none; border-radius: 25px; background: #fff; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
    .price-display { background: var(--danger-soft); border-radius: 20px; padding: 25px; margin: 20px 0; border: 2px dashed #ff4757; }
    /* ເພີ່ມໃສ່ໃນສ່ວນ <style> ທາງເທິງ */
    .select2-container--default .select2-selection--single {
        border-radius: 12px !important;
        height: 48px !important;
        padding-top: 10px !important;
        border: 1px solid #d1d3e2 !important;
        background-color: #f8f9fc !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 py-4">
        <h3 class="fw-bold text-dark mb-4"><i class="fas fa-plus-circle text-primary me-2"></i>ສ້າງການຈອງທົວໃໝ່</h3>

        <form action="save.php" method="POST" id="bookingForm" onsubmit="return validateForm()">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card booking-card p-4 p-md-5 mb-4">
                        
                        <!-- 1. ລາຍລະອຽດແພັກເກັດ -->
                        <div class="section-title"><i class="fas fa-map-marked-alt"></i>1. ລາຍລະອຽດແພັກເກັດ</div>
                        <div class="row g-3 mb-5">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">ເລືອກແພັກເກັດທົວ</label>
                                <select name="tour_id" id="tour_id" class="form-select select2" onchange="updateTourInfo()" required>
                                    <option value="" data-price="0" data-max="0" data-remain="0">-- ເລືອກທົວທີ່ຕ້ອງການ --</option>
                                    <?php 
                                    $sql_tours = "SELECT t.*, (t.max_seats - IFNULL((SELECT SUM(num_people) FROM bookings WHERE tour_id = t.tour_id AND status != 'Cancelled'), 0)) as remain FROM tours t WHERE t.status='Active' ORDER BY t.start_date ASC";
                                    $res_t = mysqli_query($conn, $sql_tours);
                                    while($t = mysqli_fetch_assoc($res_t)) {
                                        echo "<option value='{$t['tour_id']}' data-price='{$t['price']}' data-start='{$t['start_date']}' data-max='{$t['max_seats']}' data-remain='{$t['remain']}'>{$t['tour_name']} (ວ່າງ: {$t['remain']})</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ວັນທີເດີນທາງ</label>
                                <input type="date" id="travel_date" name="travel_date" class="form-control fw-bold text-primary bg-white" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ຈຳນວນຄົນ</label>
                                <input type="number" name="num_people" id="num_people" class="form-control fw-bold" value="1" min="1" oninput="handlePeopleChange()" required>
                                <div id="remain_info" class="mt-1 small fw-bold text-muted" style="display:none;">ບ່ອນນັ່ງວ່າງ: <span id="count_remain" class="text-primary">0</span> / <span id="count_total">0</span></div>
                            </div>
                        </div>

                        <!-- 2. ເລືອກລູກຄ້າຫຼັກ -->
                        <div class="section-title"><i class="fas fa-user-check"></i>2. ຂໍ້ມູນລູກຄ້າຫຼັກ</div>
                        <div class="row g-3 mb-5">
                            <div class="col-md-12">
                                <!-- ຍັບປຸ່ມມາໄວ້ແຖວດຽວກັບ Label -->
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label small fw-bold mb-0">ຊື່ລູກຄ້າຫຼັກ (Lead Customer)</label>
                                    <!-- ປຸ່ມເພີ່ມລູກຄ້າແບບໃໝ່: ກະທັດຮັດ ແລະ ໃກ້ຂຶ້ນ -->
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#quickAddCustomer">
                                        <i class="fas fa-user-plus me-1"></i> ເພີ່ມລູກຄ້າໃໝ່
                                    </button>
                                </div>
                                
                                <!-- ຫ້ອງເລືອກລູກຄ້າ -->
                                <select name="customer_id" id="customer_id" class="form-select select2" required>
                                    <option value="">-- ຊອກຫາ ຫຼື ເລືອກຊື່ລູກຄ້າ --</option>
                                    <?php 
                                    $c_res = mysqli_query($conn, "SELECT customer_id, fullname, phone FROM customers");
                                    while($c = mysqli_fetch_assoc($c_res)) {
                                        echo "<option value='{$c['customer_id']}'>{$c['fullname']} ({$c['phone']})</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- 4. ຜັງບ່ອນນັ່ງ -->
                        <div id="seatSection" style="display:none;">
                            <div class="section-title"><i class="fas fa-couch"></i>4. ຜັງບ່ອນນັ່ງ (ລ໋ອກບ່ອນນັ່ງ)</div>
                            <div class="seat-map-wrapper shadow-sm">
                                <div class="bus-head">FRONT / ທາງໜ້າ</div>
                                <div class="seat-grid" id="seatMap">
                                    <!-- ປຸ່ມບ່ອນນັ່ງຈະຂຶ້ນຢູ່ນີ້ -->
                                </div>
                                <input type="hidden" name="selected_seats" id="selected_seats_input" required>
                                
                                <div class="mt-4 d-flex justify-content-center gap-4 small fw-bold text-muted border-top pt-3">
                                    <div class="d-flex align-items-center"><span class="seat me-2" style="width:18px;height:18px;background:#f1f3f7"></span> ວ່າງ</div>
                                    <div class="d-flex align-items-center"><span class="seat selected me-2" style="width:18px;height:18px"></span> ທີ່ເລືອກ</div>
                                    <div class="d-flex align-items-center"><span class="seat occupied me-2" style="width:18px;height:18px"></span> ເຕັມ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card summary-card p-4 text-center">
                        <h5 class="fw-bold border-bottom pb-3">ສະຫຼຸບຍອດຈອງ</h5>
                        <div class="price-display">
                            <small class="text-muted d-block mb-1">ຍອດລວມທັງໝົດ</small>
                            <h1 class="text-danger fw-bold mb-0" id="display_total">0</h1>
                            <small class="fw-bold">LAK</small>
                            <input type="hidden" name="total_price" id="total_price_val">
                        </div>
                        <div class="text-start mb-4 bg-light p-3 rounded-4 small">
                            <div class="d-flex justify-content-between mb-2"><span>ບ່ອນນັ່ງ:</span><span id="summary_seats" class="fw-bold text-primary">ຍັງບໍ່ເລືອກ</span></div>
                            <div class="d-flex justify-content-between"><span>ຈຳນວນ:</span><span id="summary_people" class="fw-bold">1 ທ່ານ</span></div>
                        </div>
                        <button type="submit" name="save_booking" class="btn btn-primary btn-lg w-100 rounded-pill shadow-lg fw-bold py-3">ຢືນຢັນການຈອງ</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal ເພີ່ມລູກຄ້າ (ຂໍ້ມູນຄົບຖ້ວນ) -->
    <div class="modal fade" id="quickAddCustomer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-5 border-0 shadow-lg">
                <div class="modal-header border-0 bg-primary text-white p-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>ລົງທະບຽນລູກຄ້າໃໝ່</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickCustomerForm" enctype="multipart/form-data">
                    <div class="modal-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-7 border-end">
                                <h6 class="fw-bold text-primary mb-4 border-bottom pb-2">1. ຂໍ້ມູນສ່ວນຕົວ & ຕົວຕົນ</h6>
                                <div class="row g-3">
                                    <div class="col-md-8"><label class="form-label small fw-bold">ຊື່ ແລະ ນາມສະກຸນ</label><input type="text" name="fullname" class="form-control" required></div>
                                    <div class="col-md-4"><label class="form-label small fw-bold">ເພດ</label><select name="gender" class="form-select"><option value="Male">ຊາຍ</option><option value="Female">ຍິງ</option></select></div>
                                    <div class="col-md-6"><label class="form-label small fw-bold">ວັນເກີດ</label><input type="date" name="birthday" class="form-control"></div>
                                    <div class="col-md-6"><label class="form-label small fw-bold">ສັນຊາດ</label><input type="text" name="nationality" class="form-control" value="Lao"></div>
                                    <div class="col-md-6"><label class="form-label small fw-bold">ເລກບັດ/ພາສປອດ</label><input type="text" name="id_card_no" class="form-control"></div>
                                    <div class="col-md-6"><label class="form-label small fw-bold text-primary">ຮູບພາບບັດ (Scan)</label><input type="file" name="id_card_image" class="form-control" accept="image/*"></div>
                                    <div class="col-md-6"><label class="form-label small fw-bold">ເບີໂທລະສັບ</label><input type="text" name="phone" class="form-control" required></div>
                                    <div class="col-md-6"><label class="form-label small fw-bold">ອີເມວ</label><input type="email" name="email" class="form-control"></div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <h6 class="fw-bold text-danger mb-4 border-bottom pb-2">2. ທີ່ຢູ່ & ຕິດຕໍ່ສຸກເສີນ</h6>
                                <div class="row g-3">
                                    <div class="col-md-12"><label class="form-label small fw-bold">ທີ່ຢູ່ປະຈຸບັນ</label><textarea name="address" class="form-control" rows="3"></textarea></div>
                                    <div class="col-md-12 mt-4"><label class="form-label small fw-bold text-danger">ຊື່ຜູ້ຕິດຕໍ່ສຸກເສີນ</label><input type="text" name="emergency_name" class="form-control"></div>
                                    <div class="col-md-12"><label class="form-label small fw-bold text-danger">ເບີໂທສຸກເສີນ</label><input type="text" name="emergency_phone" class="form-control"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light p-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow fw-bold">ບັນທຶກຂໍ້ມູນລູກຄ້າ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() { 
    $('.select2').select2({ width: '100%' }); 

    $('#quickCustomerForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: 'save_customer_ajax.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const res = JSON.parse(response);
                if(res.status === 'success') {
                    const name = $('#quickCustomerForm input[name="fullname"]').val();
                    const phone = $('#quickCustomerForm input[name="phone"]').val();
                    const newOption = new Option(name + ' (' + phone + ')', res.customer_id, true, true);
                    $('#customer_id').append(newOption).trigger('change');
                    $('#quickAddCustomer').modal('hide');
                    $('#quickCustomerForm')[0].reset();
                    Swal.fire({ icon: 'success', title: 'ບັນທຶກສຳເລັດ', timer: 1500, showConfirmButton: false });
                }
            }
        });
    });
});

let selectedSeats = [];

function updateTourInfo() {
    const select = $('#tour_id');
    const selected = select.find(':selected');
    if(!selected.val()) return;

    const remain = parseInt(selected.data('remain')) || 0;
    const max = parseInt(selected.data('max')) || 0;

    $('#count_remain').text(remain);
    $('#count_total').text(max);
    $('#remain_info').show();
    $('#num_people').attr('max', remain);

    $('#travel_date').val(selected.data('start'));
    $('#seatSection').show();
    handlePeopleChange();
}

function handlePeopleChange() {
    const n = $('#num_people');
    const max = parseInt(n.attr('max'));
    if(parseInt(n.val()) > max) {
        Swal.fire({ icon: 'warning', text: `ບ່ອນນັ່ງຍັງເຫຼືອພຽງ ${max} ບ່ອນ` });
        n.val(max);
    }
    $('#summary_people').text(n.val() + ' ທ່ານ');
    generateParticipants();
    renderSeatMap();
    updateTotal();
}

function generateParticipants() {
    const num = parseInt($('#num_people').val()) || 1;
    const container = $('#participant_inputs');
    container.empty();
    if (num > 1) {
        $('#participant_section').show();
        for (let i = 2; i <= num; i++) {
            container.append(`<div class="participant-item border-start border-4 border-primary">
                <div class="row g-2">
                    <div class="col-md-7"><label class="small fw-bold">ຄົນທີ ${i}: ຊື່ເຕັມ</label><input type="text" name="participant_names[]" class="form-control" required></div>
                    <div class="col-md-5"><label class="small fw-bold">ເບີໂທ</label><input type="text" name="participant_phones[]" class="form-control"></div>
                </div></div>`);
        }
    } else { $('#participant_section').hide(); }
}

function renderSeatMap() {
    const tourId = $('#tour_id').val();
    const maxSeats = parseInt($('#tour_id').find(':selected').data('max')) || 0;
    const mapContainer = $('#seatMap');
    if(!tourId) return;

    // ສະແດງ Loading ຖ້າມີຂໍ້ມູນຫຼາຍ
    mapContainer.html('<div class="col-12 text-muted small">ກຳລັງໂຫລດຜັງ...</div>');

    fetch(`../../get_occupied_seats.php?tour_id=${tourId}`).then(res => res.json()).then(occupied => {
        mapContainer.empty(); 
        selectedSeats = []; 
        $('#selected_seats_input').val('');
        $('#summary_seats').text('ຍັງບໍ່ເລືອກ');

        for (let i = 1; i <= maxSeats; i++) {
            const sId = i.toString(); 
            const isOcc = occupied.includes(sId);
            const div = $('<div class="seat"></div>').text(i);
            
            if (isOcc) {
                div.addClass('occupied');
            } else {
                div.on('click', function() {
                    const limit = parseInt($('#num_people').val());
                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                        selectedSeats = selectedSeats.filter(s => s !== sId);
                    } else {
                        if (selectedSeats.length < limit) {
                            $(this).addClass('selected');
                            selectedSeats.push(sId);
                        } else {
                            Swal.fire({ icon: 'warning', text: `ທ່ານເລືອກໄດ້ ${limit} ບ່ອນເທົ່ານັ້ນ` });
                        }
                    }
                    $('#selected_seats_input').val(selectedSeats.join(','));
                    $('#summary_seats').text(selectedSeats.length > 0 ? selectedSeats.join(', ') : 'ຍັງບໍ່ເລືອກ');
                });
            }
            mapContainer.append(div);
        }
    }).catch(err => {
        mapContainer.html('<div class="text-danger">ບໍ່ສາມາດໂຫລດຜັງບ່ອນນັ່ງໄດ້</div>');
    });
}

function validateForm() {
    const n = parseInt($('#num_people').val());
    if (selectedSeats.length !== n) {
        Swal.fire({ icon: 'error', title: 'ເລືອກບ່ອນນັ່ງ', text: `ກະລຸນາເລືອກບ່ອນນັ່ງໃຫ້ຄົບ ${n} ບ່ອນ` });
        return false;
    }
    return true;
}

function updateTotal() {
    const pr = parseFloat($('#tour_id').find(':selected').data('price')) || 0;
    const n = parseInt($('#num_people').val()) || 1;
    const total = pr * n;
    $('#display_total').text(new Intl.NumberFormat().format(total));
    $('#total_price_val').val(total);
}
</script>
<?php include '../../includes/footer.php'; ?>