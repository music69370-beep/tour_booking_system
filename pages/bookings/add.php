<?php 
include '../../config/db.php';
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    :root { --primary-soft: #e7f1ff; --danger-soft: #fff5f6; --success-soft: #eefaf4; }
    .main-content { background-color: #f4f7f6; }
    .booking-card { border: none; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
    .section-title { font-size: 0.95rem; font-weight: 700; color: #4e73df; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 25px; display: flex; align-items: center; }
    .section-title i { margin-right: 12px; background: #4e73df; color: white; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 0.8rem; }
    .form-control, .form-select { border-radius: 12px; padding: 12px 15px; border: 1px solid #d1d3e2; background-color: #f8f9fc; font-size: 0.9rem; }
    .form-control:focus { background-color: #fff; border-color: #4e73df; box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.1); }
    
    /* Select2 Custom */
    .select2-container--default .select2-selection--single { border-radius: 12px !important; height: 48px !important; padding: 10px 12px !important; border: 1px solid #d1d3e2 !important; background-color: #f8f9fc !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 46px !important; }

    /* Room Selection Style */
    .room-card-input { cursor: pointer; border: 2px solid #f1f3f7; border-radius: 18px; transition: 0.3s; background: white; }
    .room-check:checked + .room-card-input { border-color: #4e73df; background-color: #f0f7ff; }

    /* Seat Design */
    .seat { width: 45px; height: 45px; background: #f1f3f7; border: 2px solid #e3e6f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-weight: 700; transition: 0.2s; font-size: 0.85rem; }
    .seat.selected { background: #4e73df !important; color: white !important; transform: scale(1.1); }
    .seat.occupied { background: #ff4757 !important; color: white !important; cursor: not-allowed; opacity: 0.4; }

    .participant-item { background: #f8f9fc; border-radius: 15px; padding: 15px; border: 1px solid #edf2f7; margin-bottom: 10px; }
    .summary-card { position: sticky; top: 90px; border: none; border-radius: 30px; background: #fff; box-shadow: 0 15px 40px rgba(0,0,0,0.05); }
    .price-display { background: var(--danger-soft); border-radius: 20px; padding: 25px; margin: 15px 0; border: 2px dashed #ff4757; }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 py-4">
        <h3 class="fw-bold text-dark mb-4"><i class="fas fa-plus-circle text-primary me-2"></i>ສ້າງການຈອງທົວໃໝ່ (Admin)</h3>

        <form action="save.php" method="POST" id="bookingForm" onsubmit="return validateForm()">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card booking-card p-4 p-md-5 mb-4">
                        
                        <!-- 1. ລາຍລະອຽດແພັກເກັດທົວ -->
                        <div class="section-title"><i class="fas fa-map-marked-alt"></i>1. ລາຍລະອຽດແພັກເກັດທົວ</div>
                        <div class="row g-3 mb-5">
                            <div class="col-md-12">
                                <label class="small fw-bold">ເລືອກແພັກເກັດທົວ</label>
                                <select name="tour_id" id="tour_id" class="form-select select2" onchange="updateTourInfo()" required>
                                    <option value="" data-price="0" data-max="0">-- ເລືອກແພັກເກັດ --</option>
                                    <?php 
                                    $sql_tours = "SELECT t.*, (t.max_seats - IFNULL((SELECT SUM(num_people) FROM bookings WHERE tour_id = t.tour_id AND status != 'Cancelled'), 0)) as remain FROM tours t WHERE t.status='Active'";
                                    $res_t = mysqli_query($conn, $sql_tours);
                                    while($t = mysqli_fetch_assoc($res_t)) echo "<option value='{$t['tour_id']}' data-price='{$t['price']}' data-start='{$t['start_date']}' data-max='{$t['max_seats']}' data-remain='{$t['remain']}'>{$t['tour_name']} (".date('d/m/Y', strtotime($t['start_date'])).") - [ ວ່າງ: {$t['remain']} ]</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6"><label class="small fw-bold">ວັນທີເດີນທາງ</label><input type="date" id="travel_date" name="travel_date" class="form-control bg-light border-0" readonly></div>
                            <div class="col-md-6"><label class="small fw-bold">ຈຳນວນຄົນ</label><input type="number" name="num_people" id="num_people" class="form-control bg-light border-0 fw-bold" value="1" min="1" oninput="handlePeopleChange()" required></div>
                        </div>

                        <!-- 2. ຂໍ້ມູນລູກຄ້າຫຼັກ -->
                        <div class="section-title"><i class="fas fa-user-check"></i>2. ຂໍ້ມູນລູກຄ້າຫຼັກ</div>
                        <div class="row g-3 mb-5">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="small fw-bold mb-0">ຊື່ລູກຄ້າຫຼັກ (Lead Customer)</label>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#quickAddCustomer"><i class="fas fa-plus me-1"></i> ເພີ່ມລູກຄ້າໃໝ່</button>
                                </div>
                                <select name="customer_id" id="customer_id" class="form-select select2" required>
                                    <option value="">-- ຄົ້ນຫາລູກຄ້າ --</option>
                                    <?php 
                                    $c_res = mysqli_query($conn, "SELECT customer_id, fullname, phone FROM customers");
                                    while($c = mysqli_fetch_assoc($c_res)) echo "<option value='{$c['customer_id']}'>{$c['fullname']} ({$c['phone']})</option>";
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- 3. ປະເພດຫ້ອງພັກ -->
                        <div class="section-title"><i class="fas fa-bed"></i>3. ປະເພດຫ້ອງພັກ</div>
                        <div class="row g-3 mb-5">
                            <div class="col-md-6">
                                <input type="radio" name="room_type" value="Twin" id="roomTwin" class="d-none room-check" checked onchange="updateTotal()">
                                <label for="roomTwin" class="room-card-input p-3 d-block shadow-sm">
                                    <div class="fw-bold d-flex justify-content-between"><span>ທຳມະດາ</span><span class="text-success small">ຟຣີ</span></div>
                                    <small class="text-muted">ພັກຫ້ອງຄູ່ (ນອນນຳໝູ່)</small>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <input type="radio" name="room_type" value="Single" id="roomSingle" class="d-none room-check" onchange="updateTotal()">
                                <label for="roomSingle" class="room-card-input p-3 d-block shadow-sm">
                                    <div class="fw-bold d-flex justify-content-between"><span>VIP</span><span class="text-danger small">+ 200,000</span></div>
                                    <small class="text-muted">ນອນຫ້ອງດຽວສ່ວນຕົວ</small>
                                </label>
                            </div>
                        </div>

                        <!-- 4. ລາຍຊື່ຜູ້ຮ່ວມທາງ -->
                        <div id="participant_section" style="display:none;">
                            <div class="section-title"><i class="fas fa-id-card"></i>4. ລາຍຊື່ຜູ້ຮ່ວມທາງ</div>
                            <div id="participant_inputs" class="mb-5"></div>
                        </div>

                        <!-- 5. ເລືອກບ່ອນນັ່ງ -->
                        <div id="seatSection" style="display:none;">
                            <div class="section-title"><i class="fas fa-couch"></i>5. ເລືອກບ່ອນນັ່ງ</div>
                            <div class="seat-map-wrapper shadow-sm text-center bg-light">
                                <div id="seatMap" class="d-flex flex-wrap justify-content-center gap-2 mb-4" style="max-width:450px; margin:0 auto;"></div>
                                <input type="hidden" name="selected_seats" id="selected_seats_input" required>
                                <div class="d-flex justify-content-center gap-4 small fw-bold text-muted mt-3">
                                    <div class="d-flex align-items-center"><span class="seat me-2"></span> ວ່າງ</div>
                                    <div class="d-flex align-items-center"><span class="seat selected me-2"></span> ທີ່ເລືອກ</div>
                                    <div class="d-flex align-items-center"><span class="seat occupied me-2"></span> ເຕັມ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card summary-card p-4 text-center">
                        <h5 class="fw-bold border-bottom pb-3 mb-4">ສະຫຼຸບຍອດຈອງ</h5>
                        <div class="price-display shadow-sm">
                            <h1 class="text-danger fw-bold mb-0" id="display_total">0</h1>
                            <small class="fw-bold text-danger">LAK</small>
                            <input type="hidden" name="total_price" id="total_price_val">
                        </div>
                        <div class="text-start mb-4 bg-light p-3 rounded-4 small border">
                            <div class="d-flex justify-content-between mb-2 text-dark"><span>ຈຳນວນຜູ້ເດີນທາງ:</span><span id="summary_people" class="fw-bold">1 ທ່ານ</span></div>
                            <div class="d-flex justify-content-between text-primary"><span>ບ່ອນນັ່ງທີ່ເລືອກ:</span><span id="summary_seats" class="fw-bold">ຍັງບໍ່ເລືອກ</span></div>
                        </div>
                        <button type="submit" name="save_booking" id="submitBtn" class="btn btn-primary btn-lg w-100 rounded-pill shadow-lg fw-bold py-3 mt-2">ຢືນຢັນການບັນທຶກ</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Add Customer Modal (FULL FIELDS RESTORED) -->
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
                            <!-- ຝັ່ງຊ້າຍ: ຂໍ້ມູນສ່ວນຕົວ -->
                            <div class="col-md-7 border-end pe-md-4">
                                <h6 class="fw-bold text-primary mb-4 border-bottom pb-2">1. ຂໍ້ມູນສ່ວນຕົວ & ຕົວຕົນ</h6>
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="small fw-bold">ຊື່ ແລະ ນາມສະກຸນ</label>
                                        <input type="text" name="fullname" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small fw-bold">ເພດ</label>
                                        <select name="gender" class="form-select">
                                            <option value="Male">ຊາຍ (Male)</option>
                                            <option value="Female">ຍິງ (Female)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">ວັນເດືອນປີເກີດ</label>
                                        <input type="date" name="birthday" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">ສັນຊາດ</label>
                                        <input type="text" name="nationality" class="form-control" value="Lao">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">ເລກບັດ/ພາສປອດ</label>
                                        <input type="text" name="id_card_no" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold text-primary">ຮູບພາບບັດ (Scan)</label>
                                        <input type="file" name="id_card_image" class="form-control" accept="image/*">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">ເບີໂທລະສັບ</label>
                                        <input type="text" name="phone" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">ອີເມວ</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <!-- ຝັ່ງຂວາ: ທີ່ຢູ່ & ສຸກເສີນ -->
                            <div class="col-md-5 ps-md-4">
                                <h6 class="fw-bold text-danger mb-4 border-bottom pb-2">2. ທີ່ຢູ່ & ຕິດຕໍ່ສຸກເສີນ</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="small fw-bold">ທີ່ຢູ່ປະຈຸບັນ</label>
                                        <textarea name="address" class="form-control" rows="4" placeholder="ບ້ານ, ເມືອງ, ແຂວງ..."></textarea>
                                    </div>
                                    <div class="col-md-12 mt-4">
                                        <label class="small fw-bold text-danger">ຊື່ຜູ້ຕິດຕໍ່ສຸກເສີນ</label>
                                        <input type="text" name="emergency_name" class="form-control" placeholder="ຊື່ຍາດພີ່ນ້ອງ...">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="small fw-bold text-danger">ເບີໂທສຸກເສີນ</label>
                                        <input type="text" name="emergency_phone" class="form-control" placeholder="020...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light p-4 text-center justify-content-center">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-lg fw-bold">
                            <i class="fas fa-save me-1"></i> ບັນທຶກຂໍ້ມູນລູກຄ້າໃໝ່
                        </button>
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
            processData: false, contentType: false,
            success: function(response) {
                const res = JSON.parse(response);
                if(res.status === 'success') {
                    const name = $('#quickCustomerForm input[name="fullname"]').val();
                    const phone = $('#quickCustomerForm input[name="phone"]').val();
                    const newOption = new Option(name + ' (' + phone + ')', res.customer_id, true, true);
                    $('#customer_id').append(newOption).trigger('change');
                    $('#quickAddCustomer').modal('hide');
                    $('#quickCustomerForm')[0].reset();
                    Swal.fire({ icon: 'success', title: 'ເພີ່ມລູກຄ້າສຳເລັດ', timer: 1500, showConfirmButton: false });
                }
            }
        });
    });
});

let selectedSeats = [];

function updateTourInfo() {
    const sel = $('#tour_id').find(':selected');
    if(!sel.val()) return;
    $('#travel_date').val(sel.data('start'));
    $('#num_people').attr('max', sel.data('remain'));
    $('#seatSection').show();
    handlePeopleChange();
}

function handlePeopleChange() {
    const n = $('#num_people');
    if(parseInt(n.val()) > parseInt(n.attr('max'))) { n.val(n.attr('max')); }
    $('#summary_people').text(n.val() + ' ທ່ານ');
    generateParticipants();
    renderSeatMap();
    updateTotal();
}

// ຊອກຫາຟັງຊັນນີ້ແລ້ວວາງທັບບ່ອນເກົ່າ
function generateParticipants() {
    const num = parseInt($('#num_people').val()) || 1;
    const container = $('#participant_inputs');
    container.empty();
    if (num > 1) {
        $('#participant_section').show();
        for (let i = 2; i <= num; i++) {
            container.append(`
                <div class="participant-item shadow-sm bg-white mb-3 p-3 rounded-4 border">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="small fw-bold">ຜູ້ຮ່ວມທາງທີ ${i}: ຊື່ເຕັມ</label>
                            <input type="text" name="participant_names[]" class="form-control" placeholder="ຊື່ ແລະ ນາມສະກຸນ" required>
                        </div>
                        <div class="col-md-5">
                            <!-- ປ່ຽນບ່ອນນີ້: ຈາກບັດປະຈຳຕົວ ເປັນ ເບີໂທລະສັບ -->
                            <label class="small fw-bold">ເບີໂທລະສັບ</label>
                            <input type="text" name="participant_phones[]" class="form-control" placeholder="020...">
                        </div>
                    </div>
                </div>`);
        }
    } else { 
        $('#participant_section').hide(); 
    }
}

function renderSeatMap() {
    const tourId = $('#tour_id').val();
    const maxSeats = parseInt($('#tour_id').find(':selected').data('max')) || 0;
    const mapContainer = $('#seatMap');
    if(!tourId) return;

    fetch(`../../get_occupied_seats.php?tour_id=${tourId}`).then(res => res.json()).then(occupied => {
        mapContainer.empty(); selectedSeats = []; $('#selected_seats_input').val(''); $('#summary_seats').text('ຍັງບໍ່ເລືອກ');
        for (let i = 1; i <= maxSeats; i++) {
            const sId = i.toString(); const isOcc = occupied.includes(sId);
            const div = $('<div class="seat bg-white"></div>').text(i);
            if (isOcc) div.addClass('occupied');
            else {
                div.on('click', function() {
                    const limit = parseInt($('#num_people').val());
                    if ($(this).hasClass('selected')) { $(this).removeClass('selected'); selectedSeats = selectedSeats.filter(s => s !== sId); }
                    else {
                        if (selectedSeats.length < limit) { $(this).addClass('selected'); selectedSeats.push(sId); }
                        else { Swal.fire({ icon: 'warning', text: `ເລືອກໄດ້ສູງສຸດ ${limit} ບ່ອນ` }); }
                    }
                    $('#selected_seats_input').val(selectedSeats.join(','));
                    $('#summary_seats').text(selectedSeats.length > 0 ? selectedSeats.join(', ') : 'ຍັງບໍ່ເລືອກ');
                });
            }
            mapContainer.append(div);
        }
    });
}

function updateTotal() {
    const pr = parseFloat($('#tour_id').find(':selected').data('price')) || 0;
    const n = parseInt($('#num_people').val()) || 1;
    const roomType = $('input[name="room_type"]:checked').val();
    let total = (pr * n) + (roomType === 'Single' ? 200000 : 0);
    $('#display_total').text(new Intl.NumberFormat().format(total));
    $('#total_price_val').val(total);
}

function validateForm() {
    if (selectedSeats.length !== parseInt($('#num_people').val())) {
        Swal.fire({ icon: 'error', text: 'ກະລຸນາເລືອກບ່ອນນັ່ງໃຫ້ຄົບ' });
        return false;
    }
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> ກຳລັງບັນທຶກ...';
    return true;
}
</script>

<?php include '../../includes/footer.php'; ?>