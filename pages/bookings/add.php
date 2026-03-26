<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>ສ້າງການຈອງທົວໃໝ່ (Admin)</h2>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <form action="save.php" method="POST">
                <div class="row g-4">
                    <!-- ເລືອກລູກຄ້າ -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ເລືອກລູກຄ້າ (ຫົວໜ້າກຸ່ມ)</label>
                        <select name="customer_id" class="form-select shadow-sm" required>
                            <option value="">-- ກະລຸນາເລືອກລູກຄ້າ --</option>
                            <?php 
                            $res_c = mysqli_query($conn, "SELECT customer_id, fullname, phone FROM customers");
                            while($c = mysqli_fetch_assoc($res_c)) {
                                echo "<option value='".$c['customer_id']."'>".$c['fullname']." (".$c['phone'].")</option>";
                            }
                            ?>
                        </select>
                        <div class="form-text">ຖ້າບໍ່ມີລາຍຊື່, ກະລຸນາເພີ່ມຂໍ້ມູນລູກຄ້າກ່ອນ.</div>
                    </div>

                    <!-- ເລືອກທົວ -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ເລືອກແພັກເກັດທົວ</label>
                        <select name="tour_id" id="tour_id" class="form-select shadow-sm" onchange="updateTotal(); generateParticipantFields();" required>
                            <option value="" data-price="0" data-max="0">-- ກະລຸນາເລືອກທົວ --</option>
                            <?php 
                            $res_t = mysqli_query($conn, "SELECT tour_id, tour_name, price, max_seats FROM tours WHERE status='Active'");
                            while($t = mysqli_fetch_assoc($res_t)) {
                                // ຄຳນວນບ່ອນນັ່ງຫວ່າງ
                                $tid = $t['tour_id'];
                                $booked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(num_people) as total FROM bookings WHERE tour_id = $tid AND status != 'Cancelled'"));
                                $remain = $t['max_seats'] - ($booked['total'] ?? 0);
                                
                                echo "<option value='".$t['tour_id']."' data-price='".$t['price']."' data-max='".$remain."'>".$t['tour_name']." (ຫວ່າງ: $remain / ລາຄາ: ".number_format($t['price'])." ກີບ)</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- ຈຳນວນຄົນ -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">ຈຳນວນຄົນທັງໝົດ</label>
                        <input type="number" name="num_people" id="num_people" class="form-control shadow-sm fw-bold text-primary" value="1" min="1" oninput="updateTotal(); generateParticipantFields();" required>
                    </div>

                    <!-- ລາຄາລວມ -->
                    <div class="col-md-8">
                        <label class="form-label fw-bold">ລາຄາລວມທັງໝົດ (ກີບ)</label>
                        <input type="text" id="display_total" class="form-control form-control-lg text-danger fw-bold bg-light border-0" value="0" readonly>
                        <input type="hidden" name="total_price" id="total_price">
                    </div>

                    <!-- ສ່ວນກອກລາຍຊື່ຜູ້ຮ່ວມທາງ (Dynamic) -->
                    <div id="participant_section" class="col-12" style="display:none;">
                        <div class="p-4 border rounded-4 bg-light">
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-users me-2"></i>ຂໍ້ມູນຜູ້ຮ່ວມເດີນທາງ (ນອກຈາກຫົວໜ້າກຸ່ມ)</h6>
                            <div id="participant_inputs"></div>
                        </div>
                    </div>

                    <div class="col-12 mt-5 border-top pt-4 text-end">
                        <button type="submit" name="save_booking" class="btn btn-primary btn-lg px-5 rounded-pill shadow">
                            <i class="fas fa-check-circle me-2"></i> ຢືນຢັນການຈອງ
                        </button>
                        <a href="index.php" class="btn btn-light btn-lg border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function generateParticipantFields() {
    const num = parseInt(document.getElementById('num_people').value);
    const container = document.getElementById('participant_inputs');
    const section = document.getElementById('participant_section');
    
    container.innerHTML = '';
    
    if (num > 1) {
        section.style.display = 'block';
        for (let i = 2; i <= num; i++) {
            container.innerHTML += `
                <div class="row g-2 mb-3">
                    <div class="col-md-7">
                        <input type="text" name="participant_names[]" class="form-control form-control-sm border-0 shadow-sm" placeholder="ຄົນທີ ${i}: ຊື່ ແລະ ນາມສະກຸນ" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="participant_phones[]" class="form-control form-control-sm border-0 shadow-sm" placeholder="ເບີໂທຕິດຕໍ່" required>
                    </div>
                </div>
            `;
        }
    } else {
        section.style.display = 'none';
    }
}

function updateTotal() {
    const tourSelect = document.getElementById('tour_id');
    const selected = tourSelect.options[tourSelect.selectedIndex];
    const price = selected.getAttribute('data-price');
    const maxSeats = parseInt(selected.getAttribute('data-max'));
    let numInput = document.getElementById('num_people');
    let num = parseInt(numInput.value);

    // ກວດເຊັກບ່ອນນັ່ງຫວ່າງ
    if (num > maxSeats) {
        alert("ຂໍອະໄພ! ບ່ອນນັ່ງຫວ່າງບໍ່ພໍ (ເຫຼືອ " + maxSeats + " ບ່ອນ)");
        numInput.value = maxSeats;
        num = maxSeats;
    }

    if(num < 1 || isNaN(num)) num = 1;

    const total = price * num;
    document.getElementById('display_total').value = new Intl.NumberFormat().format(total);
    document.getElementById('total_price').value = total;
}
</script>

<?php include '../../includes/footer.php'; ?>