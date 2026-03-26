<?php 
// ເປີດການເຊັກ Error (ໃຊ້ສະເພາະຕອນກຳລັງຂຽນ Code)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 

// ນັບຈຳນວນຂໍ້ມູນ
$query = mysqli_query($conn, "SELECT COUNT(*) as total FROM tours");
$count_tours = 0;
if ($query) {
    $data = mysqli_fetch_assoc($query);
    $count_tours = $data['total'];
}
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2 class="fw-bold text-dark">ແຜງຄວບຄຸມ (Dashboard)</h2>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white p-4 rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">ແພັກເກັດທົວທັງໝົດ</h6>
                        <h2 class="fw-bold mb-0"><?php echo $count_tours; ?></h2>
                    </div>
                    <i class="fas fa-map-marked-alt fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>