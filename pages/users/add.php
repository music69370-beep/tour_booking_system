<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold">ເພີ່ມບັນຊີຜູ້ໃຊ້ໃໝ່</h2>
    </div>
    <div class="card border-0 shadow-sm rounded-4 p-4" style="max-width: 600px;">
        <form action="save.php" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">ຊື່ເຕັມ (Full Name)</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">ຊື່ຜູ້ໃຊ້ (Username)</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">ລະຫັດຜ່ານ (Password)</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">ລະດັບສິດ (Role)</label>
                <select name="role" class="form-select">
                    <option value="Staff">Staff (ພະນັກງານ)</option>
                    <option value="Admin">Admin (ຜູ້ດູແລລະບົບ)</option>
                </select>
            </div>
            <button type="submit" name="save_user" class="btn btn-danger px-5 rounded-pill shadow">ບັນທຶກຜູ້ໃຊ້</button>
            <a href="index.php" class="btn btn-light border px-4 rounded-pill ms-2">ຍົກເລີກ</a>
        </form>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>