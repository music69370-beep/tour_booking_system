<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// ກວດສອບສິດ Admin
if (!isAdmin()) { header("Location: ../dashboard/index.php"); exit; }
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <!-- ສ່ວນສະແດງຂໍ້ຄວາມແຈ້ງເຕືອນ (Alerts) -->
        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3 rounded-4 border-0 shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i> ບັນທຶກຂໍ້ມູນສຳເລັດແລ້ວ!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif($_GET['msg'] == 'duplicate_code'): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3 rounded-4 border-0 shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <strong>ຜິດພາດ!</strong> ລະຫັດພະນັກງານນີ້ມີໃນລະບົບແລ້ວ.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif($_GET['msg'] == 'duplicate_user'): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3 rounded-4 border-0 shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <strong>ຜິດພາດ!</strong> ຊື່ຜູ້ນຳໃຊ້ (Username) ນີ້ມີໃນລະບົບແລ້ວ.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-users-cog text-danger me-2"></i>ຈັດການຂໍ້ມູນພະນັກງານ</h2>
            <button class="btn btn-danger rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#userModal" onclick="clearForm()">
                <i class="fas fa-user-plus me-1"></i> ເພີ່ມພະນັກງານໃໝ່
            </button>
        </div>

        <!-- ຕາຕະລາງລາຍຊື່ -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">ຮູບ</th>
                            <th>ຊື່ ແລະ ນາມສະກຸນ</th>
                            <th>ການຕິດຕໍ່ / ວັນເກີດ</th>
                            <th>ບັດປະຈຳໂຕ / ທີ່ຢູ່</th>
                            <th>ສິດ / ສະຖານະ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM users ORDER BY user_id DESC");
                        while($row = mysqli_fetch_assoc($res)):
                            // ກວດສອບຮູບໂປຣຟາຍ
                            $img_name = $row['profile_pic'];
                            $img_path = "../../assets/uploads/users/" . $img_name;
                            $display_img = (!empty($img_name) && file_exists($img_path)) ? BASE_URL . "assets/uploads/users/" . $img_name : BASE_URL . "assets/img/default-user.png";
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <img src="<?php echo $display_img; ?>" class="rounded-circle border shadow-sm" width="50" height="50" style="object-fit: cover;">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $row['fullname']; ?></div>
                                    <small class="text-muted">Code: <?php echo $row['employee_code']; ?> | User: <?php echo $row['username']; ?></small>
                                </td>
                                <td>
                                    <div class="small"><i class="fas fa-phone-alt me-1 text-muted"></i> <?php echo $row['phone']; ?></div>
                                    <div class="small"><i class="fas fa-birthday-cake me-1 text-muted"></i> <?php echo (!empty($row['dob']) ? date('d/m/Y', strtotime($row['dob'])) : '-'); ?></div>
                                </td>
                                <td style="max-width: 200px;">
                                    <div class="small fw-bold text-primary">No: <?php echo $row['id_card_no']; ?></div>
                                    <div class="small text-truncate text-muted"><?php echo $row['address']; ?></div>
                                </td>
                                <td>
                                    <div class="mb-1"><span class="badge bg-info bg-opacity-10 text-info px-2"><?php echo $row['role']; ?></span></div>
                                    <span class="badge rounded-pill <?php echo ($row['status'] == 'Active') ? 'bg-success' : 'bg-secondary'; ?> px-3">
                                        <?php echo ($row['status'] == 'Active' ? 'ກຳລັງເຮັດວຽກ' : 'ລາອອກແລ້ວ'); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                        <button class="btn btn-sm btn-white text-warning border-end" onclick='editUser(<?php echo json_encode($row); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if($row['user_id'] != $_SESSION['user_id']): ?>
                                            <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['user_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger"><i class="fas fa-trash"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal ຟອມດຽວ (Single Form) -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-5 border-0 shadow-lg">
                <div class="modal-header border-0 bg-danger text-white p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">ເພີ່ມພະນັກງານໃໝ່</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <form action="save_user_process.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4 p-md-5">
                        <input type="hidden" name="user_id" id="user_id">
                        <input type="hidden" name="old_profile_pic" id="old_profile_pic">
                        <input type="hidden" name="old_id_card_img" id="old_id_card_img">

                        <div class="row g-3">
                            <!-- ສ່ວນທີ 1: ຂໍ້ມູນສ່ວນຕົວ -->
                            <div class="col-md-12 mb-2"><h6 class="fw-bold text-danger border-start border-4 border-danger ps-2">1. ຂໍ້ມູນສ່ວນຕົວ & ການຕິດຕໍ່</h6></div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">ລະຫັດພະນັກງານ</label>
                                <input type="text" name="employee_code" id="employee_code" class="form-control bg-light border-0" placeholder="EMP-001" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">ຊື່ ແລະ ນາມສະກຸນ</label>
                                <input type="text" name="fullname" id="fullname" class="form-control bg-light border-0" placeholder="ປ້ອນຊື່ ແລະ ນາມສະກຸນ..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ເບີໂທລະສັບ</label>
                                <input type="text" name="phone" id="phone" class="form-control bg-light border-0" placeholder="020..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ອີເມວ</label>
                                <input type="email" name="email" id="email" class="form-control bg-light border-0" placeholder="example@gmail.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ວັນເດືອນປີເກີດ</label>
                                <input type="date" name="dob" id="dob" class="form-control bg-light border-0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ເລກບັດປະຈຳໂຕ / ສຳມະໂນຄົວ</label>
                                <input type="text" name="id_card_no" id="id_card_no" class="form-control bg-light border-0" placeholder="ເລກບັດປະຈຳໂຕ...">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">ທີ່ຢູ່ (ບ້ານ, ເມືອງ, ແຂວງ)</label>
                                <textarea name="address" id="address" class="form-control bg-light border-0" rows="2" placeholder="ບ້ານ, ເມືອງ, ແຂວງ..."></textarea>
                            </div>

                            <!-- ສ່ວນທີ 2: ບັນຊີ ແລະ ລະດັບສິດ -->
                            <div class="col-md-12 mt-4 mb-2 border-top pt-3"><h6 class="fw-bold text-danger border-start border-4 border-danger ps-2">2. ບັນຊີຜູ້ໃຊ້ ແລະ ລະດັບສິດ</h6></div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-primary">ຊື່ຜູ້ນຳໃຊ້ (Username)</label>
                                <input type="text" name="username" id="username" class="form-control bg-light border-0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-primary">ລະຫັດຜ່ານ (Password)</label>
                                <input type="password" name="password" id="password" class="form-control bg-light border-0">
                                <small class="text-muted" id="passHelp">ປະໄວ້ວ່າງຖ້າບໍ່ປ່ຽນ</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ລະດັບສິດ (Role)</label>
                                <select name="role" id="role" class="form-select bg-light border-0">
                                    <option value="Staff">Staff (ພະນັກງານ)</option>
                                    <option value="Admin">Admin (ແອດມິນ)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ສະຖານະ</label>
                                <select name="status" id="status" class="form-select bg-light border-0">
                                    <option value="Active">ກຳລັງເຮັດວຽກ (Active)</option>
                                    <option value="Resigned">ລາອອກແລ້ວ (Resigned)</option>
                                </select>
                            </div>

                            <!-- ສ່ວນທີ 3: ຮູບພາບເອກະສານ -->
                            <div class="col-md-12 mt-4 mb-2 border-top pt-3"><h6 class="fw-bold text-danger border-start border-4 border-danger ps-2">3. ຮູບພາບ ແລະ ເອກະສານ</h6></div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-primary">ຮູບໂປຣຟາຍ</label>
                                <input type="file" name="profile_pic" class="form-control bg-light border-0" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-primary">ຮູບພາບບັດປະຈຳໂຕ ຫຼື ອື່ນໆ</label>
                                <input type="file" name="id_card_img" class="form-control bg-light border-0" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light p-4 justify-content-center">
                        <button type="submit" name="save_user" class="btn btn-danger btn-lg px-5 rounded-pill shadow fw-bold">
                            <i class="fas fa-save me-2"></i> ບັນທຶກຂໍ້ມູນທັງໝົດ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
function clearForm() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus me-2"></i>ເພີ່ມພະນັກງານໃໝ່';
    document.getElementById('user_id').value = '';
    document.getElementById('employee_code').value = '';
    document.getElementById('fullname').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('email').value = '';
    document.getElementById('dob').value = '';
    document.getElementById('id_card_no').value = '';
    document.getElementById('address').value = '';
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
    document.getElementById('password').required = true;
    document.getElementById('passHelp').innerText = "ກະລຸນາກຳນົດລະຫັດຜ່ານ";
    document.getElementById('status').value = 'Active';
    document.getElementById('role').value = 'Staff';
}

function editUser(data) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-edit me-2"></i>ແກ້ໄຂຂໍ້ມູນພະນັກງານ';
    document.getElementById('user_id').value = data.user_id;
    document.getElementById('employee_code').value = data.employee_code;
    document.getElementById('fullname').value = data.fullname;
    document.getElementById('phone').value = data.phone;
    document.getElementById('email').value = data.email;
    document.getElementById('dob').value = data.dob;
    document.getElementById('id_card_no').value = data.id_card_no;
    document.getElementById('address').value = data.address;
    document.getElementById('username').value = data.username;
    document.getElementById('role').value = data.role;
    document.getElementById('status').value = data.status;
    document.getElementById('old_profile_pic').value = data.profile_pic;
    document.getElementById('old_id_card_img').value = data.id_card_img;

    document.getElementById('password').value = '';
    document.getElementById('password').required = false;
    document.getElementById('passHelp').innerText = "ປະໄວ້ວ່າງຖ້າບໍ່ຕ້ອງການປ່ຽນລະຫັດ";

    var myModal = new bootstrap.Modal(document.getElementById('userModal'));
    myModal.show();
}
</script>

<style>
    .modal-dialog-scrollable .modal-body { max-height: 75vh; }
    .form-control:focus { box-shadow: none; border: 1px solid #dc3545 !important; }
    .btn-white { background: #fff; border: none; } .btn-white:hover { background: #f8f9fa; }
</style>

<?php include '../../includes/footer.php'; ?>