<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h2 class="fw-bold text-dark"><i class="fas fa-calendar-alt text-primary me-2"></i>ປະຕິທິນການຈອງທົວ</h2>
            <div class="d-flex gap-2">
                <span class="badge bg-success rounded-pill px-3 py-2">ຢືນຢັນແລ້ວ</span>
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">ລໍຖ້າອະນຸມັດ</span>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <!-- ບ່ອນວາງປະຕິທິນ -->
            <div id='calendar'></div>
        </div>
    </div>
</main>

<!-- FullCalendar Library -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'lo', // ພາສາລາວ (ຖ້າ Library ຮອງຮັບ)
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        events: 'get_events.php', // ດຶງຂໍ້ມູນຈາກໄຟລ໌ JSON ທີ່ເຮົາສ້າງ
        eventClick: function(info) {
            // ເມື່ອກົດທີ່ລາຍການ ໃຫ້ເປີດໜ້າເບິ່ງລາຍລະອຽດ
            if (info.event.url) {
                window.location.href = info.event.url;
                info.jsEvent.preventDefault();
            }
        },
        height: 'auto'
    });
    calendar.render();
});
</script>

<style>
    #calendar {
        max-width: 100%;
        margin: 0 auto;
        font-family: 'Noto Sans Lao', sans-serif;
    }
    .fc-event {
        cursor: pointer;
        padding: 2px 5px;
        border-radius: 4px;
        font-size: 0.85rem;
    }
    .fc-toolbar-title {
        font-weight: bold;
        color: #333;
    }
</style>

<?php include '../../includes/footer.php'; ?>