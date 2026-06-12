<?php 
include 'config/db.php'; 
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

$sql = "SELECT b.*, c.fullname, c.phone, t.tour_name, t.image, t.meeting_point 
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.customer_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        WHERE b.booking_id = '$id' AND b.status = 'Confirmed'";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
if (!$row) { exit("Ticket Not Found or Not Confirmed"); }
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>Ticket #BK-<?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background: #f8f9fa; }
        .ticket-card { max-width: 600px; margin: 50px auto; border-radius: 20px; border: none; overflow: hidden; }
        .seat-badge { background: #0d6efd; color: white; padding: 5px 15px; border-radius: 50px; font-weight: bold; margin-right: 5px; }
    </style>
</head>
<body>
    <div class="container text-center no-print mt-4"><button onclick="window.print()" class="btn btn-primary">ພິມໃບຢັ້ງຢືນ</button></div>
    <div class="card ticket-card shadow-lg">
        <div class="bg-primary text-white p-4 text-center">
            <h2 class="fw-bold mb-0">Tour Booking Ticket</h2>
            <p class="mb-0 opacity-75">ID: #BK-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></p>
        </div>
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark"><?php echo $row['tour_name']; ?></h5>
            <hr>
            <p><strong>ຜູ້ເດີນທາງ:</strong> <?php echo $row['fullname']; ?></p>
            <p><strong>ວັນທີເດີນທາງ:</strong> <span class="text-danger fw-bold"><?php echo date('d/m/Y', strtotime($row['travel_date'])); ?></span></p>
            <p><strong>ຈຸດນັດພົບ:</strong> <?php echo $row['meeting_point']; ?></p>
            
            <div class="mt-4 p-3 bg-light rounded-3">
                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-couch me-2"></i>ໝາຍເລກບ່ອນນັ່ງຂອງທ່ານ:</h6>
                <?php 
                $seats = explode(',', $row['selected_seats']);
                foreach($seats as $s) echo "<span class='seat-badge'>$s</span>";
                ?>
            </div>
            
            <div class="mt-4 text-center">
                <p class="small text-muted">ກະລຸນານັ່ງຕາມໝາຍເລກທີ່ລະບຸໄວ້ເທິງລົດ</p>
                <h4 class="fw-bold text-success">ຈ່າຍແລ້ວ: <?php echo number_format($row['total_price']); ?> LAK</h4>
            </div>
        </div>
    </div>
</body>
</html>