<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ລະບົບຈອງທົວ - TourBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        
        body { 
            font-family: 'Noto Sans Lao', sans-serif; 
            background-color: #f4f6f9; 
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* ປ້ອງກັນການເລື່ອນແນວນອນ */
        }

        /* Sidebar: ໃຫ້ຕິດຢູ່ເບື້ອງຊ້າຍຕະຫຼອດ */
        .sidebar { 
            height: 100vh; 
            background: #fff; 
            border-right: 1px solid #ddd; 
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto; /* ໃຫ້ Sidebar scroll ໄດ້ຖ້າມັນຍາວ */
        }

        /* Main Content: ໃຫ້ຍັບອອກມາຈາກ Sidebar ແລະ ໃຫ້ Scroll ໄດ້ປົກກະຕິ */
        .main-content { 
            margin-left: 16.666667%; /* ໃຫ້ຕົງກັບ col-md-2 ຂອງ Bootstrap */
            min-height: 100vh;
            width: 83.333333%;
            display: flex;
            flex-direction: column;
            background-color: #f4f6f9;
        }

        .swal2-popup { font-family: 'Noto Sans Lao', sans-serif !important; }
        .font-lao { font-family: 'Noto Sans Lao', sans-serif; }

        /* ປັບແຕ່ງໃຫ້ມືຖື */
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>
<div class="container-fluid p-0">
    <div class="row g-0">