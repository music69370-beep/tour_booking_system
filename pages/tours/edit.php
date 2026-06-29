<?php 
include '../../config/db.php'; 
/** @var mysqli $conn */
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = '$id'"));
if (!$row) exit("Tour not found");
?>

<!-- ເພີ່ມ Leaflet CSS/JS ສຳລັບແຜນທີ່ -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-edit text-warning me-2"></i>ແກ້ໄຂແພັກເກັດທົວ</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍົກເລີກ</a>
        </div>

        <form action="update.php" method="POST" enctype="multipart/form-data" id="tourForm">
            <input type="hidden" name="tour_id" value="<?php echo $row['tour_id']; ?>">
            <input type="hidden" name="old_image" value="<?php echo $row['image']; ?>">

            <div class="row g-4">
                <!-- 1. ຂໍ້ມູນແພັກເກັດ & ວັນທີ -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-info-circle me-2"></i>1. ຂໍ້ມູນແພັກເກັດ & ວັນທີ</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">ລະຫັດແພັກເກັດ</label>
                                <input type="text" name="tour_code" class="form-control bg-light border-0 py-2" value="<?php echo $row['tour_code']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຊື່ແພັກເກັດທົວ</label>
                                <input type="text" name="tour_name" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($row['tour_name']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">ໝວດໝູ່</label>
                                <select name="category" class="form-select bg-light border-0 py-2">
                                    <option value="ທົວວັດທະນະທຳ" <?php if($row['category']=='ທົວວັດທະນະທຳ') echo 'selected'; ?>>ທົວວັດທະນະທຳ</option>
                                    <option value="ທົວຜະຈົນໄພ" <?php if($row['category']=='ທົວຜະຈົນໄພ') echo 'selected'; ?>>ທົວຜະຈົນໄພ</option>
                                    <option value="ທົວພັກຜ່ອນ" <?php if($row['category']=='ທົວພັກຜ່ອນ') echo 'selected'; ?>>ທົວພັກຜ່ອນ</option>
                                </select>
                            </div>
                            <!-- ເພີ່ມສ່ວນເລືອກໄກ້ ໃນ pages/tours/edit.php -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-primary">ໄກ້ຜູ້ນຳທ່ຽວ (Guide)</label>
                                <select name="guide_id" class="form-select bg-light border-0 py-2 shadow-none" required>
                                    <option value="">-- ເລືອກໄກ້ --</option>
                                    <?php 
                                    // ດຶງລາຍຊື່ໄກ້ທັງໝົດ ທີ່ "ວ່າງ" ຫຼື "ແມ່ນຄົນທີ່ຖືກເລືອກໄວ້ແລ້ວ"
                                    $current_guide = $row['guide_id'];
                                    $g_res = mysqli_query($conn, "SELECT guide_id, fullname FROM guides WHERE status='Available' OR guide_id='$current_guide'");
                                    while($g = mysqli_fetch_assoc($g_res)) {
                                        $selected = ($g['guide_id'] == $current_guide) ? 'selected' : '';
                                        echo "<option value='{$g['guide_id']}' $selected>{$g['fullname']}</option>";
                                    }
                                    ?>
                                </select>
                                <!-- ສົ່ງ ID ໄກ້ເກົ່າໄປນຳ ເພື່ອໃຊ້ຈັດການສະຖານະໃນພາຍຫຼັງ -->
                                <input type="hidden" name="old_guide_id" value="<?php echo $current_guide; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-danger">ວັນທີເລີ່ມເດີນທາງ</label>
                                <input type="date" name="start_date" class="form-control border-danger py-2" value="<?php echo $row['start_date']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-danger">ວັນທີສິ້ນສຸດ</label>
                                <input type="date" name="end_date" class="form-control border-danger py-2" value="<?php echo $row['end_date']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-primary">ລາຄາຂາຍ/ທ່ານ</label>
                                <input type="number" name="price" class="form-control border-primary py-2" value="<?php echo $row['price']; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ລາຍລະອຽດການບໍລິການ -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-info"><i class="fas fa-list me-2"></i>2. ລາຍລະອຽດການບໍລິການ</h5>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="fw-bold small">ບ່ອນນັ່ງສູງສຸດ</label><input type="number" name="max_seats" class="form-control bg-light border-0 py-2" value="<?php echo $row['max_seats']; ?>" required></div>
                            <div class="col-md-4"><label class="fw-bold small">ຈຳນວນຄົນຂັ້ນຕ່ຳ</label><input type="number" name="min_pax" class="form-control bg-light border-0 py-2" value="<?php echo $row['min_pax']; ?>"></div>
                            <div class="col-md-4"><label class="fw-bold small">ອາຫານ (ຄາບ)</label><input type="number" name="meals" class="form-control bg-light border-0 py-2" value="<?php echo $row['meals']; ?>"></div>
                            <div class="col-md-12"><label class="fw-bold small">ໄລຍະເວລາ (ເຊັ່ນ: 2 ວັນ 1 ຄືນ)</label><input type="text" name="duration" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($row['duration']); ?>"></div>
                            <div class="col-md-12"><label class="fw-bold small">ສະຖານທີ່ນັດພົບ</label><input type="text" name="meeting_point" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($row['meeting_point']); ?>"></div>
                        </div>
                    </div>
                </div>

                <!-- ສ່ວນຈັດການຮູບພາບໃນ pages/tours/edit.php -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h5 class="fw-bold mb-4 text-warning"><i class="fas fa-image me-2"></i>3. ຈັດການຮູບພາບ</h5>
                        
                        <!-- 1. ຮູບໜ້າປົກ -->
                        <div class="mb-4 p-3 bg-light rounded-4 border">
                            <label class="form-label fw-bold small text-primary">ຮູບໜ້າປົກ (Cover Image)</label>
                            <input type="file" name="image" class="form-control bg-white border-0 small mb-2" accept="image/*">
                            <div class="text-center">
                                <img src="../../assets/uploads/tours/<?php echo $row['image']; ?>" class="rounded shadow-sm border" width="120">
                            </div>
                        </div>

                        <!-- 2. ຮູບ Gallery (ຮູບເພີ່ມເຕີມ) -->
                        <div class="p-3 bg-light rounded-4 border">
                            <label class="form-label fw-bold small text-primary">ເພີ່ມຮູບ Gallery (ເລືອກໄດ້ຫຼາຍຮູບ)</label>
                            <input type="file" name="gallery[]" class="form-control bg-white border-0 small mb-3" accept="image/*" multiple>
                            
                            <label class="d-block small fw-bold mb-2 text-muted">ຮູບ Gallery ປັດຈຸບັນ:</label>
                            <div class="row g-2">
                                <?php 
                                $gal_res = mysqli_query($conn, "SELECT * FROM tour_images WHERE tour_id = '$id'");
                                while($g = mysqli_fetch_assoc($gal_res)): ?>
                                    <div class="col-4 position-relative mb-2">
                                        <img src="../../assets/uploads/tours/<?php echo $g['image_name']; ?>" class="w-100 rounded border shadow-xs" style="height: 60px; object-fit: cover;">
                                        <!-- ປຸ່ມລຶບຮູບ Gallery ເທື່ອລະໃບ -->
                                        <a href="delete_gallery.php?img_id=<?php echo $g['image_id']; ?>&tour_id=<?php echo $id; ?>" 
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 px-1 rounded-circle shadow-sm" 
                                        onclick="return confirm('ລຶບຮູບນີ້ແທ້ບໍ?')" 
                                        style="width: 20px; height: 20px; line-height: 15px; font-size: 12px;">&times;</a>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. ແຜນການເດີນທາງລະອຽດ -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-map-marked-alt text-primary me-2"></i>4. ແຜນການເດີນທາງລະອຽດ</h5>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" onclick="addDay()">+ ເພີ່ມມື້ເດີນທາງ</button>
                        </div>
                        <div id="itinerary-container"></div>
                        <input type="hidden" name="itinerary" id="itinerary_json">
                    </div>
                </div>

                <!-- 5. ລາຍລະອຽດ ແລະ ນະໂຍບາຍ -->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-file-alt me-2"></i>5. ລາຍລະອຽດ ແລະ ນະໂຍບາຍ</h5>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="fw-bold small">ຈຸດເດັ່ນ (Highlights)</label><textarea name="highlights" class="form-control bg-light border-0" rows="3"><?php echo $row['highlights']; ?></textarea></div>
                            <div class="col-md-4"><label class="fw-bold small text-success">ສິ່ງທີ່ລວມ</label><textarea name="whats_included" class="form-control bg-light border-0" rows="3"><?php echo $row['whats_included']; ?></textarea></div>
                            <div class="col-md-4"><label class="fw-bold small text-danger">ສິ່ງທີ່ບໍ່ລວມ</label><textarea name="whats_excluded" class="form-control bg-light border-0" rows="3"><?php echo $row['whats_excluded']; ?></textarea></div>
                            <div class="col-md-6"><label class="fw-bold small">ນະໂຍບາຍການຍົກເລີກ</label><textarea name="cancellation_policy" class="form-control bg-light border-0" rows="2"><?php echo $row['cancellation_policy']; ?></textarea></div>
                            <div class="col-md-6"><label class="fw-bold small">ກິດຈະກຳ</label><textarea name="activities" class="form-control bg-light border-0" rows="2"><?php echo $row['activities']; ?></textarea></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mt-5 mb-5">
                    <button type="submit" name="update_tour" class="btn btn-warning btn-lg px-5 rounded-pill shadow fw-bold text-white">
                        <i class="fas fa-save me-2"></i> ບັນທຶກການປ່ຽນແປງ
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<!-- Map Picker Modal -->
<div class="modal fade" id="mapPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0"><h5 class="fw-bold">ເລືອກສະຖານທີ່</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
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
const oldData = <?php echo $row['itinerary'] ?: '[]'; ?>;

function addDay(dayData = null) {
    dayCount++;
    const dayHtml = `
        <div class="day-block shadow-sm" id="day-${dayCount}">
            <div class="d-flex justify-content-between mb-3"><h6 class="fw-bold text-primary">ມື້ທີ ${dayCount}</h6><i class="fas fa-trash text-danger cursor-pointer" onclick="document.getElementById('day-${dayCount}').remove()"></i></div>
            <div id="events-day-${dayCount}"></div>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="addEvent(${dayCount})">+ ເພີ່ມຈຸດໝາຍ</button>
        </div>`;
    document.getElementById('itinerary-container').insertAdjacentHTML('beforeend', dayHtml);
    if (dayData && dayData.events) {
        dayData.events.forEach(ev => addEvent(dayCount, ev));
    } else { addEvent(dayCount); }
}

function addEvent(dayId, evData = null) {
    const eventId = Date.now() + Math.floor(Math.random() * 1000);
    const eventHtml = `
        <div class="event-item" id="event-${eventId}">
            <div class="row g-2 align-items-center">
                <div class="col-md-2">
                    <select class="form-select form-select-sm border-0 bg-light ev-type">
                        <option value="start" ${evData?.type=='start'?'selected':''}>🚩 ເລີ່ມ</option>
                        <option value="visit" ${evData?.type=='visit'?'selected':''}>📍 ທ່ຽວ</option>
                        <option value="hotel" ${evData?.type=='hotel'?'selected':''}>🏨 ພັກ</option>
                        <option value="eat" ${evData?.type=='eat'?'selected':''}>🍴 ກິນ</option>
                        <option value="end" ${evData?.type=='end'?'selected':''}>🏁 ຈົບ</option>
                    </select>
                </div>
                <div class="col-md-3"><input type="text" class="form-control form-control-sm border-0 bg-light ev-loc" placeholder="ສະຖານທີ່" value="${evData?.location || ''}"></div>
                <div class="col-md-3"><input type="text" class="form-control form-control-sm border-0 bg-light ev-desc" placeholder="ລາຍລະອຽດ" value="${evData?.desc || ''}"></div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control border-0 bg-light ev-latlng" placeholder="Lat, Lng" value="${evData?.lat ? evData.lat+','+evData.lng : ''}" readonly>
                        <button type="button" class="btn btn-dark px-2" onclick="openMapPicker('${eventId}')"><i class="fas fa-map-marker-alt"></i></button>
                    </div>
                </div>
                <div class="col-md-1 text-center"><i class="fas fa-times text-muted cursor-pointer" onclick="document.getElementById('event-${eventId}').remove()"></i></div>
            </div>
        </div>`;
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

if (oldData.length > 0) oldData.forEach(day => addDay(day));
else addDay();
</script>

<?php include '../../includes/footer.php'; ?>