<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <!-- 1. ໃສ່ Navbar ບ່ອນນີ້ -->
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>ສ້າງການຈອງທົວໃໝ່</h2>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <form action="save.php" method="POST">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ເລືອກລູກຄ້າ</label>
                        <select name="customer_id" class="form-select shadow-sm" required>
                            <option value="">-- ກະລຸນາເລືອກລູກຄ້າ --</option>
                            <?php 
                            $res_c = mysqli_query($conn, "SELECT customer_id, fullname FROM customers");
                            while($c = mysqli_fetch_assoc($res_c)) echo "<option value='".$c['customer_id']."'>".$c['fullname']."</option>";
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ເລືອກແພັກເກັດທົວ</label>
                        <select name="tour_id" id="tour_id" class="form-select shadow-sm" onchange="calculatePrice()" required>
                            <option value="" data-price="0">-- ກະລຸນາເລືອກທົວ --</option>
                            <?php 
                            $res_t = mysqli_query($conn, "SELECT tour_id, tour_name, price FROM tours WHERE status='Active'");
                            while($t = mysqli_fetch_assoc($res_t)) echo "<option value='".$t['tour_id']."' data-price='".$t['price']."'>".$t['tour_name']." (".number_format($t['price'])." ກີບ/ຄົນ)</option>";
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">ຈຳນວນຄົນ</label>
                        <input type="number" name="num_people" id="num_people" class="form-control shadow-sm" value="1" min="1" oninput="calculatePrice()" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">ລາຄາລວມທັງໝົດ (ກີບ)</label>
                        <input type="text" id="display_total" class="form-control form-control-lg text-danger fw-bold bg-light border-0 shadow-none" value="0" readonly>
                        <input type="hidden" name="total_price" id="total_price">
                    </div>
                    <div class="col-12 mt-5">
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
function calculatePrice() {
    const tourSelect = document.getElementById('tour_id');
    const pricePerPerson = tourSelect.options[tourSelect.selectedIndex].getAttribute('data-price');
    const numPeople = document.getElementById('num_people').value;
    const total = pricePerPerson * numPeople;
    document.getElementById('display_total').value = new Intl.NumberFormat().format(total);
    document.getElementById('total_price').value = total;
}
</script>

<?php include '../../includes/footer.php'; ?>