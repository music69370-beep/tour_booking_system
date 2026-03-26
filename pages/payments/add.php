<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold">ບັນທຶກການຮັບເງິນ</h2>
    </div>
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="save.php" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">ເລືອກລາຍການຈອງ (ທີ່ຍັງບໍ່ທັນຈ່າຍ)</label>
                    <select name="booking_id" class="form-select" required>
                        <option value="">-- ເລືອກລາຍການຈອງ --</option>
                        <?php 
                        // ດຶງສະເພາະການຈອງທີ່ເປັນ Pending
                        $res_b = mysqli_query($conn, "SELECT b.booking_id, c.fullname, b.total_price 
                                                     FROM bookings b 
                                                     JOIN customers c ON b.customer_id = c.customer_id 
                                                     WHERE b.status = 'Pending'");
                        while($b = mysqli_fetch_assoc($res_b)) {
                            echo "<option value='".$b['booking_id']."'>#BK-".$b['booking_id']." - ".$b['fullname']." (ຍອດເງິນ: ".number_format($b['total_price'])." ກີບ)</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">ຊ່ອງທາງການຈ່າຍ</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="BCEL One">BCEL One</option>
                        <option value="Cash">ເງິນສົດ</option>
                        <option value="Transfer">ໂອນທະນາຄານອື່ນ</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">ອັບໂຫລດໃບບິນ (Slip)</label>
                    <input type="file" name="payment_slip" class="form-control" accept="image/*" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">ວັນທີ ແລະ ເວລາທີ່ຊຳລະ</label>
                    <input type="datetime-local" name="payment_date" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                </div>
                <div class="col-12 mt-5">
                    <button type="submit" name="save_payment" class="btn btn-success btn-lg px-5 rounded-pill shadow">ຢືນຢັນການຮັບເງິນ</button>
                    <a href="index.php" class="btn btn-light btn-lg border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
                </div>
            </div>
        </form>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>