<?php 
include '../../config/db.php'; include '../../includes/header.php'; include '../../includes/sidebar.php'; 
?>
<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>
    <div class="px-4 pb-5">
        <h2 class="fw-bold text-dark pt-3 pb-2 mb-4 border-bottom"><i class="fas fa-star text-warning me-2"></i>ຈັດການຄຳຍ້ອງຍໍ (Reviews)</h2>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-uppercase">
                        <tr>
                            <th class="ps-4">ວັນທີ</th>
                            <th>ລູກຄ້າ</th>
                            <th>ແພັກເກັດທົວ</th>
                            <th class="text-center">ຄະແນນ</th>
                            <th>ຄຳຄິດເຫັນ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT r.*, c.fullname, t.tour_name 
                                FROM reviews r 
                                JOIN customers c ON r.customer_id=c.customer_id 
                                JOIN tours t ON r.tour_id=t.tour_id 
                                ORDER BY r.review_id DESC";
                        $res = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($res)):
                        ?>
                        <tr>
                            <td class="ps-4 small text-muted"><?php echo date('d/m/Y', strtotime($row['review_date'])); ?></td>
                            <td class="fw-bold"><?php echo $row['fullname']; ?></td>
                            <td class="small"><?php echo $row['tour_name']; ?></td>
                            <td class="text-center">
                                <span class="text-warning">
                                    <?php for($i=1; $i<=5; $i++) echo ($i <= $row['rating']) ? '<i class="fas fa-star"></i>':'<i class="far fa-star"></i>'; ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?php echo $row['comment']; ?></td>
                            <td class="text-center">
                                <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['review_id']; ?>, 'delete.php')" class="btn btn-sm btn-white text-danger shadow-sm border rounded-pill">
                                    <i class="fas fa-trash"></i> ລຶບ
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<style> .btn-white { background:#fff; border:none; } .btn-white:hover { background:#f8f9fa; } </style>
<?php include '../../includes/footer.php'; ?>