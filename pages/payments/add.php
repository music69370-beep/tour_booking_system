<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-cash-register text-success me-2"></i>ບັນທຶກການຮັບເງິນ</h2>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <form action="save.php" method="POST" enctype="multipart/form-data">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ເລືອກລາຍການຈອງ (ສະເພາະທີ່ຍັງບໍ່ທັນຈ່າຍ)</label>
                                <select name="booking_id" id="booking_id" class="form-select bg-light border-0 shadow-none" onchange="updateAmount()" required>
                                    <option value="" data-amount="0">-- ເລືອກລາຍການຈອງ --</option>
                                    <?php 
                                    // ດຶງສະເພາະການຈອງທີ່ Pending ແລະ ຍັງບໍ່ມີຂໍ້ມູນໃນ table payments
                                    $sql = "SELECT b.booking_id, c.fullname, b.total_price 
                                            FROM bookings b 
                                            JOIN customers c ON b.customer_id = c.customer_id 
                                            WHERE b.status = 'Pending' 
                                            AND b.booking_id NOT IN (SELECT booking_id FROM payments)";
                                    $res_b = mysqli_query($conn, $sql);
                                    while($b = mysqli_fetch_assoc($res_b)) {
                                        echo "<option value='".$b['booking_id']."' data-amount='".$b['total_price']."'>#BK-".$b['booking_id']." - ".$b['fullname']." (ຍອດ: ".number_format($b['total_price'])." ກີບ)</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຈຳນວນເງິນທີ່ຮັບ (ກີບ)</label>
                                <input type="number" name="amount" id="amount" class="form-control bg-white border-success fw-bold text-danger" placeholder="0" readonly required>
                                <small class="text-muted small">* ລະບົບດຶງຍອດເງິນອັດຕະໂນມັດ</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ຊ່ອງທາງການຮັບເງິນ</label>
                                <select name="payment_method" class="form-select bg-light border-0 shadow-none" required>
                                    <option value="Cash">Cash (ເງິນສົດ)</option>
                                    <option value="BCEL One">BCEL One</option>
                                    <option value="Transfer">Transfer (ໂອນທະນາຄານ)</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ອັບໂຫລດໃບບິນ/ຫຼັກຖານ (ຖ້າມີ)</label>
                                <input type="file" name="payment_slip" class="form-control bg-light border-0 shadow-none small" accept="image/*">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ວັນທີ ແລະ ເວລາທີ່ຮັບເງິນ</label>
                                <input type="datetime-local" name="payment_date" class="form-control bg-light border-0 shadow-none" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                            </div>

                            <div class="col-12 mt-5 pt-3 border-top text-end">
                                <button type="submit" name="save_payment" class="btn btn-success px-5 rounded-pill shadow">
                                    <i class="fas fa-check-circle me-2"></i> ຢືນຢັນການຮັບເງິນ
                                </button>
                                <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold"><i class="fas fa-info-circle me-2"></i>ໝາຍເຫດ</h5>
                    <p class="small mb-0">ຟອມນີ້ໃຊ້ສຳລັບແອດມິນບັນທຶກການຮັບເງິນ **ດ້ວຍຕົນເອງ** (ເຊັ່ນ: ລູກຄ້າມາຈ່າຍເງິນສົດຢູ່ຫ້ອງການ). ເມື່ອບັນທຶກແລ້ວ ສະຖານະການຈອງຈະກາຍເປັນ <b>"ຢືນຢັນແລ້ວ"</b> ທັນທີ.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function updateAmount() {
    const select = document.getElementById('booking_id');
    const amountInput = document.getElementById('amount');
    const selectedOption = select.options[select.selectedIndex];
    const amount = selectedOption.getAttribute('data-amount');
    amountInput.value = amount ? amount : 0;
}
</script>

<?php include '../../includes/footer.php'; ?>