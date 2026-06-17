<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$id = mysqli_real_escape_string($conn, $_GET['id']);
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$id'"));
if (!$row) exit;
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-edit text-warning me-2"></i>ແກ້ໄຂແພັກເກັດທົວ</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍ້ອນກັບ</a>
        </div>

        <form action="update.php" method="POST" enctype="multipart/form-data" id="tourForm">
            <input type="hidden" name="tour_id" value="<?php echo $row['tour_id']; ?>">
            <input type="hidden" name="old_image" value="<?php echo $row['image']; ?>">

            <div class="row g-4">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <div class="row g-3">
                            <div class="col-md-4"><label class="fw-bold small">ລະຫັດ</label><input type="text" name="tour_code" class="form-control bg-light border-0" value="<?php echo $row['tour_code']; ?>" required></div>
                            <div class="col-md-8"><label class="fw-bold small">ຊື່ແພັກເກັດ</label><input type="text" name="tour_name" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($row['tour_name']); ?>" required></div>
                            <div class="col-md-6"><label class="fw-bold small">ລາຄາ</label><input type="number" name="price" class="form-control border-primary" value="<?php echo $row['price']; ?>" required></div>
                            <div class="col-md-6"><label class="fw-bold small">ບ່ອນນັ່ງ</label><input type="number" name="max_seats" class="form-control bg-light border-0" value="<?php echo $row['max_seats']; ?>" required></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex justify-content-between mb-4"><h5 class="fw-bold">ຈັດການແຜນການເດີນທາງ</h5><button type="button" class="btn btn-primary btn-sm rounded-pill" onclick="addDay()">+ ເພີ່ມມື້</button></div>
                        <div id="itinerary-container"></div>
                        <input type="hidden" name="itinerary" id="itinerary_json">
                    </div>
                </div>

                <div class="col-12 text-center mt-5"><button type="submit" name="update_tour" class="btn btn-warning px-5 rounded-pill shadow fw-bold">ບັນທຶກການປ່ຽນແປງ</button></div>
            </div>
        </form>
    </div>
</main>

<!-- Map Picker Modal (ຄືກັນກັບ add.php) -->
<div class="modal fade" id="mapPickerModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow"><div class="modal-header border-0"><h5 class="fw-bold">ເລືອກສະຖານທີ່</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-0"><div id="map-picker" style="height: 450px;"></div></div><div class="modal-footer border-0"><button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">ຕົກລົງ</button></div></div></div></div>

<style>.day-block { background: #f8f9fc; border-radius: 20px; padding: 20px; margin-bottom: 20px; border: 1px solid #e3e6f0; } .event-item { background: white; padding: 15px; border-radius: 12px; margin-bottom: 10px; border: 1px solid #eee; } .cursor-pointer { cursor: pointer; }</style>

<script>
let dayCount = 0;
let currentPickerTarget = null;
let pickerMap, pickerMarker;

// ດຶງຂໍ້ມູນເກົ່າຈາກ PHP
const oldData = <?php echo $row['itinerary'] ?: '[]'; ?>;

function addDay(dayData = null) {
    dayCount++;
    const dayHtml = `
        <div class="day-block shadow-sm" id="day-${dayCount}">
            <div class="d-flex justify-content-between mb-3"><h6 class="fw-bold text-primary">ມື້ທີ ${dayCount}</h6><i class="fas fa-trash text-danger cursor-pointer" onclick="document.getElementById('day-${dayCount}').remove()"></i></div>
            <div id="events-day-${dayCount}"></div>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="addEvent(${dayCount})">+ ເພີ່ມຈຸດໝາຍ</button>
        </div>
    `;
    document.getElementById('itinerary-container').insertAdjacentHTML('beforeend', dayHtml);
    
    if (dayData && dayData.events) {
        dayData.events.forEach(ev => addEvent(dayCount, ev));
    } else {
        addEvent(dayCount);
    }
}

function addEvent(dayId, evData = null) {
    const eventId = Date.now() + Math.floor(Math.random() * 1000);
    const eventHtml = `
        <div class="event-item" id="event-${eventId}">
            <div class="row g-2">
                <div class="col-md-2">
                    <select class="form-select form-select-sm border-0 bg-light ev-type">
                        <option value="start" ${evData?.type=='start'?'selected':''}>🚩 ເລີ່ມ</option>
                        <option value="visit" ${evData?.type=='visit'?'selected':''}>📍 ທ່ຽວ</option>
                        <option value="eat" ${evData?.type=='eat'?'selected':''}>🍴 ກິນ</option>
                        <option value="hotel" ${evData?.type=='hotel'?'selected':''}>🏨 ພັກ</option>
                        <option value="end" ${evData?.type=='end'?'selected':''}>🏁 ຈົບ</option>
                    </select>
                </div>
                <div class="col-md-3"><input type="text" class="form-control form-control-sm border-0 bg-light ev-loc" placeholder="ຊື່ສະຖານທີ່" value="${evData?.location || ''}"></div>
                <div class="col-md-3"><input type="text" class="form-control form-control-sm border-0 bg-light ev-desc" placeholder="ລາຍລະອຽດ/ເວລາ" value="${evData?.desc || ''}"></div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control border-0 bg-light ev-latlng" placeholder="ຄລິກແຜນທີ່ ->" value="${evData?.lat ? evData.lat+','+evData.lng : ''}" readonly>
                        <button type="button" class="btn btn-dark" onclick="openMapPicker('${eventId}')"><i class="fas fa-map-marker-alt"></i></button>
                    </div>
                </div>
                <div class="col-md-1"><i class="fas fa-times text-muted cursor-pointer" onclick="document.getElementById('event-${eventId}').remove()"></i></div>
            </div>
        </div>
    `;
    document.getElementById(`events-day-${dayId}`).insertAdjacentHTML('beforeend', eventHtml);
}

// Map Picker Function (ຄືກັນກັບ add.php)
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
                type: ev.querySelector('.ev-type').value, location: ev.querySelector('.ev-loc').value,
                desc: ev.querySelector('.ev-desc').value, lat: latlng[0] || '', lng: latlng[1] || ''
            });
        });
        data.push(dayObj);
    });
    document.getElementById('itinerary_json').value = JSON.stringify(data);
});

// Load ຂໍ້ມູນເກົ່າ
if (oldData.length > 0) oldData.forEach(day => addDay(day));
else addDay();

</script>
<?php include '../../includes/footer.php'; ?>