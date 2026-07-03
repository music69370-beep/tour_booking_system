<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<!-- ເພີ່ມ Leaflet CSS/JS ສໍາລັບແຜນທີ່ -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-folder-plus text-primary me-2"></i>ເພີ່ມແພັກເກັດທົວໃໝ່</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍົກເລີກ</a>
        </div>

        <form action="save.php" method="POST" enctype="multipart/form-data" id="tourForm">
            <div class="row g-4">
                <!-- 1. ຂໍ້ມູນທົ່ວໄປ -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-info-circle me-2"></i>1. ຂໍ້ມູນແພັກເກັດ</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">ລະຫັດແພັກເກັດ</label>
                                <input type="text" name="tour_code" class="form-control bg-light border-0 py-2" placeholder="PKG-001" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold small">ຊື່ແພັກເກັດທົວ</label>
                                <input type="text" name="tour_name" class="form-control bg-light border-0 py-2" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ໝວດໝູ່</label>
                                <select name="category" class="form-select bg-light border-0 py-2">
                                    <option value="ທົວວັດທະນະທຳ">ທົວວັດທະນະທຳ</option>
                                    <option value="ທົວຜະຈົນໄພ">ທົວຜະຈົນໄພ</option>
                                    <option value="ທົວພັກຜ່ອນ">ທົວພັກຜ່ອນ</option>
                                </select>
                            </div>
                            <!-- ເພີ່ມສ່ວນເລືອກໄກ້ (Guide) ເຂົ້າໃນ pages/tours/add.php -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-primary">ເລືອກໄກ້ຜູ້ນຳທ່ຽວປະຈຳແພັກເກັດ</label>
                                <select name="guide_id" class="form-select bg-light border-0 py-2 shadow-none" required>
                                    <option value="">-- ເລືອກໄກ້ຜູ້ນຳທ່ຽວ --</option>
                                    <?php 
                                    // SQL ໃໝ່: ດຶງໄກ້ທັງໝົດທີ່ "ບໍ່ໄດ້ຖືທົວທີ່ກຳລັງ Active ຢູ່"
                                        $g_res = mysqli_query($conn, "SELECT guide_id, fullname FROM guides 
                                                                    WHERE guide_id NOT IN (
                                                                        SELECT guide_id FROM tours 
                                                                        WHERE status = 'Active' AND guide_id IS NOT NULL
                                                                    )");

                                        while($g = mysqli_fetch_assoc($g_res)) {
                                            echo "<option value='{$g['guide_id']}'>{$g['fullname']}</option>";
                                        }
                                    ?>
                                </select>
                                <small class="text-muted" style="font-size: 0.7rem;">* ຈະສະແດງສະເພາະໄກ້ທີ່ມີສະຖານະ "ວ່າງ"</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-primary">ລາຄາຂາຍ/ທ່ານ</label>
                                <input type="number" name="price" class="form-control border-primary py-2" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-danger">ວັນທີເລີ່ມ</label>
                                <input type="date" name="start_date" class="form-control border-danger py-2" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-danger">ວັນທີສິ້ນສຸດ</label>
                                <input type="date" name="end_date" class="form-control border-danger py-2" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">ບ່ອນນັ່ງສູງສຸດ</label>
                                <input type="number" name="max_seats" class="form-control bg-light border-0 py-2" value="10" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold small">ສະຖານທີ່ນັດພົບ</label>
                                <input type="text" name="meeting_point" class="form-control bg-light border-0 py-2">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ຮູບພາບ -->
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

                <!-- 3. ແຜນການເດີນທາງ (ບ່ອນທີ່ແກ້ໄຂ) -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-map-marked-alt text-primary me-2"></i>3. ແຜນການເດີນທາງລະອຽດ (Map Builder)</h5>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" onclick="addDay()">+ ເພີ່ມມື້ເດີນທາງ</button>
                        </div>

                        <div id="itinerary-container"></div>
                        <input type="hidden" name="itinerary" id="itinerary_json">

                        <!-- ສ່ວນອື່ນໆ -->
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

<!-- ປ່ອງຢ້ຽມເລືອກແຜນທີ່ (Modal) -->
<div class="modal fade" id="mapPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0"><h5 class="fw-bold">ຄລິກໃສ່ແຜນທີ່ເພື່ອເລືອກສະຖານທີ່</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-0"><div id="map-picker" style="height: 450px;"></div></div>
            <div class="modal-footer border-0"><button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">ຕົກລົງ</button></div>
        </div>
    </div>
</div>

<style>
    .day-block { background: #f8f9fc; border-radius: 20px; padding: 20px; margin-bottom: 20px; border: 1px solid #e3e6f0; }
    .event-item { background: white; padding: 15px; border-radius: 12px; margin-bottom: 10px; border: 1px solid #eee; }
    .cursor-pointer { cursor: pointer; }
</style>

<script>
let dayCount = 0;
let currentPickerTarget = null;
let pickerMap, pickerMarker;

function addDay() {
    dayCount++;
    const dayHtml = `
        <div class="day-block shadow-sm" id="day-${dayCount}">
            <div class="d-flex justify-content-between mb-3"><h6 class="fw-bold text-primary">ມື້ທີ ${dayCount}</h6><i class="fas fa-trash text-danger cursor-pointer" onclick="document.getElementById('day-${dayCount}').remove()"></i></div>
            <div id="events-day-${dayCount}"></div>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="addEvent(${dayCount})">+ ເພີ່ມຈຸດໝາຍ</button>
        </div>
    `;
    document.getElementById('itinerary-container').insertAdjacentHTML('beforeend', dayHtml);
    addEvent(dayCount);
}

function addEvent(dayId) {
    const eventId = Date.now();
    const eventHtml = `
        <div class="event-item" id="event-${eventId}">
            <div class="row g-2">
                <div class="col-md-2">
                    <select class="form-select form-select-sm border-0 bg-light ev-type">
                        <option value="start">🚩 ເລີ່ມ</option><option value="visit" selected>📍 ທ່ຽວ</option><option value="eat">🍴 ກິນ</option><option value="hotel">🏨 ພັກ</option><option value="end">🏁 ຈົບ</option>
                    </select>
                </div>
                <div class="col-md-3"><input type="text" class="form-control form-control-sm border-0 bg-light ev-loc" placeholder="ຊື່ສະຖານທີ່"></div>
                <div class="col-md-3"><input type="text" class="form-control form-control-sm border-0 bg-light ev-desc" placeholder="ລາຍລະອຽດ/ເວລາ"></div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control border-0 bg-light ev-latlng" placeholder="ຄລິກແຜນທີ່ ->" readonly>
                        <button type="button" class="btn btn-dark" onclick="openMapPicker('${eventId}')"><i class="fas fa-map-marker-alt"></i></button>
                    </div>
                </div>
                <div class="col-md-1"><i class="fas fa-times text-muted cursor-pointer" onclick="document.getElementById('event-${eventId}').remove()"></i></div>
            </div>
        </div>
    `;
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
            const latlng = ev.querySelector('.ev-latlng').value.split(',');
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

<?php include '../../includes/footer.php'; ?>