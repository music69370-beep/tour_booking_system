<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// ກວດສອບສິດ Admin
if (!isAdmin()) {
    header("Location: ../dashboard/index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$res = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id'");
$row = mysqli_fetch_assoc($res);

if (!$row) { 
    echo "<div class='container mt-5'><div class='alert alert-danger font-lao text-center'>ບໍ່ພົບຂໍ້ມູນຜູ້ໃຊ້ນີ້!</div></div>";
    exit; 
}
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <!-- ເອີ້ນໃຊ້ Navbar (ໂຊຊື່ຜູ້ໃຊ້ງານມຸມຂວາເທິງ) -->
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="fw-bold text-dark">
                <i class="fas fa-user-edit text-warning me-2"></i>ແກ້ໄຂບັນຊີຜູ້ໃຊ້
            </h2>
            <a href="index.php" class="btn btn-light border rounded-pill px-4 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> ຍ້ອນກັບ
            </a>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <form action="update.php" method="POST">
                        <!-- ສົ່ງ ID ຜູ້ໃຊ້ໄປເບື້ອງຫຼັງ -->
                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                        
                        <div class="row g-4">
                            <!-- ຊື່ເຕັມ -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">ຊື່ ແລະ ນາມສະກຸນ</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-id-card text-muted"></i></span>
                                    <input type="text" name="fullname" class="form-control bg-light border-0 shadow-none" 
                                           value="<?php echo htmlspecialchars($row['fullname']); ?>" required>
                                </div>
                            </div>

                            <!-- ຊື່ຜູ້ໃຊ້ -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">ຊື່ຜູ້ໃຊ້ (Username)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-user-circle text-muted"></i></span>
                                    <input type="text" name="username" class="form-control bg-light border-0 shadow-none" 
                                           value="<?php echo htmlspecialchars($row['username']); ?>" required>
                                </div>
                            </div>

                            <!-- ລະຫັດຜ່ານ -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">ລະຫັດຜ່ານໃໝ່ (ປະໄວ້ຖ້າບໍ່ຕ້ອງການປ່ຽນ)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-key text-muted"></i></span>
                                    <input type="password" name="password" class="form-control bg-light border-0 shadow-none" 
                                           placeholder="••••••••">
                                </div>
                                <div class="form-text text-warning small mt-2">
                                    <i class="fas fa-info-circle me-1"></i> ຖ້າທ່ານບໍ່ປ້ອນຫຍັງ ລະບົບຈະຮັກສາລະຫັດຜ່ານເດີມໄວ້.
                                </div>
                            </div>

                            <!-- ລະດັບສິດ -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">ລະດັບສິດການນຳໃຊ້</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-user-tag text-muted"></i></span>
                                    <select name="role" class="form-select bg-light border-0 shadow-none">
                                        <option value="Staff" <?php if($row['role'] == 'Staff') echo 'selected'; ?>>Staff (ພະນັກງານ)</option>
                                        <option value="Admin" <?php if($row['role'] == 'Admin') echo 'selected'; ?>>Admin (ຜູ້ດູແລລະບົບ)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ປຸ່ມບັນທຶກ -->
                            <div class="col-12 mt-5 pt-3 border-top">
                                <button type="submit" name="update_user" class="btn btn-warning w-100 py-3 rounded-pill shadow fw-bold">
                                    <i class="fas fa-save me-2"></i> ບັນທຶກການປ່ຽນແປງ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ສ່ວນຄຳແນະນຳດ້ານຂ້າງ -->
            <div class="col-lg-5 ms-lg-auto">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border-start border-warning border-5">
                    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>ຂໍ້ຄວນລະວັງ</h5>
                    <ul class="mb-0 small text-muted" style="line-height: 1.8;">
                        <li>ການແກ້ໄຂ <b>ຊື່ຜູ້ໃຊ້ (Username)</b> ຈະມີຜົນຕໍ່ການເຂົ້າລະບົບໃນຄັ້ງຖັດໄປ.</li>
                        <li>ການປ່ຽນ <b>ລະດັບສິດ</b> ຈະມີຜົນຕໍ່ການເຂົ້າເຖິງເມນູຕ່າງໆໃນ Sidebar ທັນທີ.</li>
                        <li>ຫາກທ່ານແກ້ໄຂຂໍ້ມູນຂອງຕົວເອງ, ລະບົບຈະເຮັດການອັບເດດ Session ຂອງທ່ານໂດຍອັດຕະໂນມັດ.</li>
                    </ul>
                </div>

                <div class="mt-4 text-center">
                    <i class="fas fa-shield-alt fa-5x text-light opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    /* ຕົບແຕ່ງ Input Group */
    .input-group-text {
        border-radius: 12px 0 0 12px !important;
        border: 1px solid #f8f9fa;
    }
    .form-control, .form-select {
        border-radius: 0 12px 12px 0 !important;
        padding: 12px 15px;
    }
    .form-control:focus {
        background-color: #fff !important;
        border: 1px solid #ffc107 !important;
    }
    .card {
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
</style>

<?php include '../../includes/footer.php'; ?>