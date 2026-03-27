<?php 
include 'config/db.php'; 
$phone = isset($_GET['phone']) ? mysqli_real_escape_string($conn, $_GET['phone']) : '';
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ກວດສອບສະຖານະ - TourBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fa; }
        .status-card { border: none; border-radius: 20px; transition: all 0.3s; }
        .star-rating { color: #ccc; font-size: 1.5rem; cursor: pointer; transition: 0.2s; }
        .star-rating.active { color: #ffc107; }
        .star-rating:hover { transform: scale(1.2); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-5">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-plane-departure me-2"></i>TourBooking</a>
        <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">ກັບໄປໜ້າຫຼັກ</a>
    </div>
</nav>

<div class="container pb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">ສະຖານະການຈອງຂອງທ່ານ</h2>
        <p class="text-muted">ຄົ້ນຫາດ້ວຍເບີໂທລະສັບ: <span class="text-primary fw-bold"><?php echo $phone; ?></span></p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <?php 
            if ($phone != '') {
                $sql = "SELECT b.*, c.fullname, t.tour_name, t.tour_id, t.image
                        FROM bookings b
                        JOIN customers c ON b.customer_id = c.customer_id
                        JOIN tours t ON b.tour_id = t.tour_id
                        WHERE c.phone LIKE '%$phone%'
                        ORDER BY b.booking_id DESC";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $status = $row['status'];
                        $is_confirmed = ($status == 'Confirmed');
                        $bg_color = ($is_confirmed) ? 'bg-success' : (($status == 'Cancelled') ? 'bg-danger' : 'bg-warning text-dark');
            ?>
                <div class="card status-card shadow-sm mb-4 overflow-hidden">
                    <div class="card-header <?php echo $bg_color; ?> text-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <span class="fw-bold">#BK-<?php echo $row['booking_id']; ?> | <?php echo $row['tour_name']; ?></span>
                        <span class="badge bg-white text-dark rounded-pill"><?php echo ($status == 'Confirmed') ? 'ຢືນຢັນແລ້ວ' : (($status == 'Cancelled') ? 'ຍົກເລີກແລ້ວ' : 'ລໍຖ້າອະນຸມັດ'); ?></span>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="fw-bold mb-1">ຜູ້ຈອງ: <?php echo $row['fullname']; ?></h6>
                                <p class="text-muted small mb-0">ວັນທີເດີນທາງ: <?php echo date('d/m/Y', strtotime($row['travel_date'])); ?> | ຈຳນວນ: <?php echo $row['num_people']; ?> ຄົນ</p>
                                <h5 class="text-danger fw-bold mt-2"><?php echo number_format($row['total_price']); ?> ກີບ</h5>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <?php if($is_confirmed): ?>
                                    <button class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $row['booking_id']; ?>">
                                        <i class="fas fa-star me-1"></i> ໃຫ້ຄະແນນທົວ
                                    </button>
                                <?php else: ?>
                                    <small class="text-muted">ສາມາດໃຫ້ຄະແນນໄດ້ຫຼັງຈາກຢືນຢັນແລ້ວ</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal ສໍາລັບໃຫ້ຄະແນນ -->
                <div class="modal fade" id="reviewModal<?php echo $row['booking_id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0">
                            <form action="save_review.php" method="POST">
                                <div class="modal-header border-0 bg-warning text-dark">
                                    <h5 class="modal-title fw-bold">ແບ່ງປັນຄວາມປະທັບໃຈ</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 text-center">
                                    <input type="hidden" name="tour_id" value="<?php echo $row['tour_id']; ?>">
                                    <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>">
                                    
                                    <img src="assets/uploads/tours/<?php echo $row['image']; ?>" class="rounded-3 mb-3 shadow-sm" width="100" height="70" style="object-fit: cover;">
                                    <h6 class="fw-bold mb-4">ທ່ານໃຫ້ຄະແນນ "<?php echo $row['tour_name']; ?>" ເທົ່າໃດ?</h6>

                                    <div class="mb-4">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <input type="radio" name="rating" value="<?php echo $i; ?>" id="r<?php echo $row['booking_id'].$i; ?>" class="d-none" required>
                                            <label for="r<?php echo $row['booking_id'].$i; ?>" class="star-rating s-<?php echo $row['booking_id']; ?>" onclick="updateStars(<?php echo $row['booking_id']; ?>, <?php echo $i; ?>)">
                                                <i class="fas fa-star"></i>
                                            </label>
                                        <?php endfor; ?>
                                    </div>

                                    <div class="text-start">
                                        <label class="form-label small fw-bold">ບອກຄວາມຮູ້ສຶກຂອງທ່ານ:</label>
                                        <textarea name="comment" class="form-control bg-light border-0 shadow-none" rows="3" placeholder="ຂຽນຄຳຄິດເຫັນ..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold shadow">ສົ່ງຄຳຍ້ອງຍໍ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php 
                    }
                } else {
                    echo "<div class='text-center py-5 text-muted'>ບໍ່ພົບຂໍ້ມູນການຈອງ</div>";
                }
            }
            ?>
        </div>
    </div>
</div>

<script>
function updateStars(bkId, rating) {
    const labels = document.querySelectorAll('.s-' + bkId);
    labels.forEach((label, index) => {
        if (index < rating) {
            label.classList.add('active');
        } else {
            label.classList.remove('active');
        }
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>