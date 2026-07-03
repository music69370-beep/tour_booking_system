<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<!-- 1. ໂຫຼດ Script ທີ່ຈຳເປັນ (ບັງຄັບໂຫຼດເພື່ອໃຫ້ Select2 ເຮັດວຽກໄດ້ແນ່ນອນ) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Leaflet CSS/JS ສໍາລັບແຜນທີ່ (ຮັກສາໄວ້ຄືເກົ່າ) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-calendar-check text-primary me-2"></i>ເພີ່ມແພັກເກັດທົວ (ຈັດຕາຕະລາງໄກ້)</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍົກເລີກ</a>
        </div>

        <!-- ແຈ້ງເຕືອນໃຫ້ເລືອກວັນທີກ່ອນ -->
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
            <div>
                <strong class="d-block">ຄຳເຕືອນ: ກະລຸນາເລືອກວັນທີເດີນທາງກ່ອນ!</strong>
                <span class="small">ລະບົບຈະກວດສອບລາຍຊື່ໄກ້ທີ່ "ວ່າງ" ໃຫ້ທ່ານເລືອກ ຕາມຊ່ວງວັນທີທີ່ທ່ານກຳນົດເທົ່ານັ້ນ.</span>
            </div>
        </div>

        <form action="save.php" method="POST" enctype="multipart/form-data" id="tourForm">
            <div class="row g-4">
                <!-- 1. ຂໍ້ມູນແພັກເກັດ -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-info-circle me-2"></i>1. ຂໍ້ມູນແພັກເກັດ & ວັນທີ</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">ລະຫັດແພັກເກັດ</label>
                                <input type="text" name="tour_code" class="form-control bg-light border-0 py-2" placeholder="PKG-001" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold small">ຊື່ແພັກເກັດທົວ</label>
                                <input type="text" name="tour_name" class="form-control bg-light border-0 py-2" required>
                            </div>
                            
                            <!-- ສ່ວນວັນທີ: ເມື່ອປ່ຽນວັນທີ ຈະໄປເອີ້ນ AJAX -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-danger">ວັນທີເລີ່ມເດີນທາງ</label>
                                <input type="date" name="start_date" id="start_date" class="form-control border-danger py-2" required onchange="checkDateAndLoadGuides()">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-danger">ວັນທີສິ້ນສຸດ</label>
                                <input type="date" name="end_date" id="end_date" class="form-control border-danger py-2" required onchange="checkDateAndLoadGuides()">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ໝວດໝູ່</label>
                                <select name="category" class="form-select bg-light border-0 py-2">
                                    <option value="ທົວວັດທະນະທຳ">ທົວວັດທະນະທຳ</option>
                                    <option value="ທົວຜະຈົນໄພ">ທົວຜະຈົນໄພ</option>
                                    <option value="ທົວພັກຜ່ອນ">ທົວພັກຜ່ອນ</option>
                                </select>
                            </div>
                            
                            <!-- ສ່ວນເລືອກໄກ້: ຖືກ Lock ໄວ້ໃນຕອນທຳອິດ -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-primary">ເລືອກໄກ້ຜູ້ນຳທ່ຽວ (ເລືອກໄດ້ຫຼາຍຄົນ)</label>
                                <select name="guide_ids[]" id="guide_select" class="form-control select2-multiple" multiple="multiple" disabled required>
                                    <!-- ລາຍຊື່ຈະມາຈາກ AJAX -->
                                </select>
                                <div id="guide_loader" class="mt-2" style="display:none;">
                                    <span class="text-primary small"><i class="fas fa-spinner fa-spin me-1"></i> ກຳລັງໂຫຼດລາຍຊື່ໄກ້ທີ່ວ່າງ...</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-primary">ລາຄາຂາຍ/ທ່ານ</label>
                                <input type="number" name="price" class="form-control border-primary py-2" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ບ່ອນນັ່ງສູງສຸດ</label>
                                <input type="number" name="max_seats" class="form-control bg-light border-0 py-2" value="10" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ສະຖານທີ່ນັດພົບ</label>
                                <input type="text" name="meeting_point" class="form-control bg-light border-0 py-2">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ຮູບພາບ (ຮັກສາໄວ້ຄືເກົ່າ) -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-warning"><i class="fas fa-image me-2"></i>2. ຮູບພາບ</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">ຮູບໜ້າປົກ</label>
                            <input type="file" name="image" class="form-control bg-light border-0 small" accept="image/*" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small">ຮູບ Gallery (ຫຼາຍຮູບ)</label>
                            <input type="file" name="gallery[]" class="form-control bg-light border-0 small" accept="image/*" multiple>
                        </div>
                    </div>
                </div>

                <!-- 3. ແຜນການເດີນທາງ (Map Builder - ຮັກສາໄວ້ຄືເກົ່າ) -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-map-marked-alt text-primary me-2"></i>3. ແຜນການເດີນທາງລະອຽດ</h5>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" onclick="addDay()">+ ເພີ່ມມື້ເດີນທາງ</button>
                        </div>
                        <div id="itinerary-container"></div>
                        <input type="hidden" name="itinerary" id="itinerary_json">
                        <div class="row g-3 mt-4">
                            <div class="col-md-4"><label class="fw-bold small">ຈຸດເດັ່ນ</label><textarea name="highlights" class="form-control bg-light border-0" rows="3"></textarea></div>
                            <div class="col-md-4"><label class="fw-bold small text-success">ສິ່ງທີ່ລວມ</label><textarea name="whats_included" class="form-control bg-light border-0" rows="3"></textarea></div>
                            <div class="col-md-4"><label class="fw-bold small text-danger">ສິ່ງທີ່ບໍ່ລວມ</label><textarea name="whats_excluded" class="form-control bg-light border-0" rows="3"></textarea></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-5 mb-5">
                    <button type="submit" name="save_tour" class="btn btn-primary btn-lg px-5 rounded-pill shadow fw-bold">ບັນທຶກແພັກເກັດທົວ</button>
                </div>
            </div>
        </form>
    </div>
</main>

<!-- Modal ຕ່າງໆ -->
<div class="modal fade" id="mapPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0"><h5 class="fw-bold">ເລືອກສະຖານທີ່</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-0"><div id="map-picker" style="height: 450px;"></div></div>
            <div class="modal-footer border-0"><button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">ຕົກລົງ</button></div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // ເລີ່ມຕົ້ນ Select2 ແບບ Multiple
    $('#guide_select').select2({
        placeholder: " -- ກະລຸນາເລືອກວັນທີກ່ອນ -- ",
        allowClear: true,
        width: '100%'
    });
});

// --- ຟັງຊັນ AJAX ດຶງໄກ້ທີ່ວ່າງຕາມວັນທີ ---
function checkDateAndLoadGuides() {
    const start = $('#start_date').val();
    const end = $('#end_date').val();

    if (start && end) {
        if (start > end) {
            alert("ວັນທີເລີ່ມ ຕ້ອງບໍ່ກາຍວັນທີສິ້ນສຸດ!");
            $('#guide_select').prop('disabled', true).val(null).trigger('change');
            return;
        }

        // ເປີດໃຫ້ເລືອກ ແລະ ໂຊ Loader
        $('#guide_loader').show();
        
        $.ajax({
            url: 'get_available_guides.php', // ໄຟລ໌ກວດສອບວັນທີ
            type: 'GET',
            data: { start: start, end: end },
            dataType: 'json',
            success: function(data) {
                $('#guide_loader').hide();
                $('#guide_select').prop('disabled', false);
                
                let options = '';
                data.forEach(function(guide) {
                    options += `<option value="${guide.guide_id}">${guide.fullname}</option>`;
                });
                
                $('#guide_select').html(options).trigger('change');
                $('#guide_select').select2({ placeholder: " -- ເລືອກໄກ້ ("+data.length+" ຄົນທີ່ວ່າງ) -- " });
            }
        });
    } else {
        // ຖ້າວັນທີບໍ່ຄົບ ໃຫ້ Lock ໄວ້ຄືເກົ່າ
        $('#guide_select').prop('disabled', true).val(null).trigger('change');
        $('#guide_select').select2({ placeholder: " -- ກະລຸນາເລືອກວັນທີກ່ອນ -- " });
    }
}

// --- ຟັງຊັນອື່ນໆ (Map & Itinerary) ໃຫ້ປະໄວ້ຄືເກົ່າ 100% ---
let dayCount = 0;
let currentPickerTarget = null;
let pickerMap, pickerMarker;

function addDay() {
    dayCount++;
    const dayHtml = `<div class="day-block shadow-sm" id="day-${dayCount}"><div class="d-flex justify-content-between mb-3"><h6 class="fw-bold text-primary">ມື້ທີ ${dayCount}</h6><i class="fas fa-trash text-danger cursor-pointer" onclick="document.getElementById('day-${dayCount}').remove()"></i></div><div id="events-day-${dayCount}"></div><button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="addEvent(${dayCount})">+ ເພີ່ມຈຸດໝາຍ</button></div>`;
    document.getElementById('itinerary-container').insertAdjacentHTML('beforeend', dayHtml);
    addEvent(dayCount);
}

function addEvent(dayId) {
    const eventId = Date.now() + Math.floor(Math.random() * 100);
    const eventHtml = `<div class="event-item" id="event-${eventId}"><div class="row g-2"><div class="col-md-2"><select class="form-select form-select-sm border-0 bg-light ev-type"><option value="start">🚩 ເລີ່ມ</option><option value="visit" selected>📍 ທ່ຽວ</option><option value="eat">🍴 ກິນ</option><option value="hotel">🏨 ພັກ</option><option value="end">🏁 ຈົບ</option></select></div><div class="col-md-3"><input type="text" class="form-control form-control-sm border-0 bg-light ev-loc" placeholder="ຊື່ສະຖານທີ່"></div><div class="col-md-3"><input type="text" class="form-control form-control-sm border-0 bg-light ev-desc" placeholder="ລາຍລະອຽດ/ເວລາ"></div><div class="col-md-3"><div class="input-group input-group-sm"><input type="text" class="form-control border-0 bg-light ev-latlng" placeholder="Lat, Lng" readonly><button type="button" class="btn btn-dark" onclick="openMapPicker('${eventId}')"><i class="fas fa-map-marker-alt"></i></button></div></div><div class="col-md-1"><i class="fas fa-times text-muted cursor-pointer" onclick="document.getElementById('event-${eventId}').remove()"></i></div></div></div>`;
    document.getElementById(`events-day-${dayId}`).insertAdjacentHTML('beforeend', eventHtml);
}

function openMapPicker(targetId) {
    currentPickerTarget = targetId;
    const modal = new bootstrap.Modal(document.getElementById('mapPickerModal'));
    modal.show();
    if (!pickerMap) {
        setTimeout(() => {
            pickerMap = L.map('map-picker').setView([17.9757, 102.6331], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(pickerMap);
            pickerMap.on('click', function(e) {
                const { lat, lng } = e.latlng;
                if (pickerMarker) pickerMarker.setLatLng(e.latlng);
                else pickerMarker = L.marker(e.latlng).addTo(pickerMap);
                document.querySelector(`#event-${currentPickerTarget} .ev-latlng`).value = `${lat.toFixed(6)},${lng.toFixed(6)}`;
            });
        }, 300);
    }
}

document.getElementById('tourForm').addEventListener('submit', function() {
    let data = [];
    document.querySelectorAll('.day-block').forEach((day, i) => {
        let dayObj = { day: i+1, events: [] };
        day.querySelectorAll('.event-item').forEach(ev => {
            const latlngValue = ev.querySelector('.ev-latlng').value;
            const latlng = latlngValue ? latlngValue.split(',') : ['', ''];
            dayObj.events.push({
                type: ev.querySelector('.ev-type').value,
                location: ev.querySelector('.ev-loc').value,
                desc: ev.querySelector('.ev-desc').value,
                lat: latlng[0] || '', lng: latlng[1] || ''
            });
        });
        data.push(dayObj);
    });
    document.getElementById('itinerary_json').value = JSON.stringify(data);
});

addDay();
</script>

<style>
    .day-block { background: #f8f9fc; border-radius: 20px; padding: 20px; margin-bottom: 20px; border: 1px solid #e3e6f0; } 
    .event-item { background: white; padding: 15px; border-radius: 12px; margin-bottom: 10px; border: 1px solid #eee; } 
    .cursor-pointer { cursor: pointer; }
    .select2-container--default .select2-selection--multiple {
        background-color: #f8f9fc !important;
        border: 1px solid #d1d3e2 !important;
        border-radius: 12px !important;
        min-height: 45px;
        padding: 5px;
    }
</style>

<?php include '../../includes/footer.php'; ?>