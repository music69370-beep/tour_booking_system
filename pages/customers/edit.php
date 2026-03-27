<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

$id = mysqli_real_escape_string($conn, $_GET['id']);
$res = mysqli_query($conn, "SELECT * FROM customers WHERE customer_id = '$id'");
$row = mysqli_fetch_assoc($res);

if (!$row) { echo "ບໍ່ພົບຂໍ້ມູນລູກຄ້າ"; exit; }
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-user-edit text-warning me-2"></i>ແກ້ໄຂຂໍ້ມູນລູກຄ້າ</h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">ຍົກເລີກ</a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4" style="max-width: 800px;">
            <form action="update.php" method="POST">
                <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>">
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ຊື່ ແລະ ນາມສະກຸນ</label>
                        <input type="text" name="fullname" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($row['fullname']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ເບີໂທລະສັບ</label>
                        <input type="text" name="phone" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($row['phone']); ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">ອີເມວ</label>
                        <input type="email" name="email" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($row['email']); ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">ທີ່ຢູ່</label>
                        <textarea name="address" class="form-control bg-light border-0 py-2" rows="3"><?php echo htmlspecialchars($row['address']); ?></textarea>
                    </div>
                    
                    <div class="col-12 mt-4 pt-3 border-top">
                        <button type="submit" name="update_customer" class="btn btn-warning px-5 rounded-pill shadow fw-bold">
                            <i class="fas fa-save me-2"></i> ບັນທຶກການປ່ຽນແປງ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>