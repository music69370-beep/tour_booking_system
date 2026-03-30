<?php 
include '../../config/db.php'; 
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
?>

<style>
    /* --- ປັບແຕ່ງ FullCalendar ໃຫ້ສະບາຍຕາ --- */
    :root {
        --fc-border-color: #f0f0f0;
        --fc-daygrid-event-dot-width: 8px;
        --fc-today-bg-color: rgba(13, 110, 253, 0.05);
    }

    .main-content { background-color: #f8f9fa; }

    #calendar {
        max-width: 100%;
        margin: 0 auto;
        font-family: 'Noto Sans Lao', sans-serif;
        background: white;
    }

    /* ປັບແຕ່ງຫົວຕາຕະລາງ (Sun, Mon...) */
    .fc-col-header-cell {
        background-color: #f8f9fa;
        padding: 12px 0 !important;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        font-size: 0.85rem;
        border-bottom: 2px solid #dee2e6 !important;
    }

    /* ປັບແຕ່ງກ່ອງວັນທີ */
    .fc-daygrid-day {
        transition: all 0.2s;
    }
    .fc-daygrid-day:hover {
        background-color: #fafafa;
    }

    /* ປັບແຕ່ງເຫດການ (Events) */
    .fc-event {
        border: none !important;
        padding: 4px 8px !important;
        margin: 2px 4px !important;
        border-radius: 6px !important;
        font-size: 0.8rem !important;
        font-weight: 500 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* ປັບແຕ່ງປຸ່ມກົດ (Toolbar Buttons) */
    .fc-button {
        background-color: #ffffff !important;
        border: 1px solid #dee2e6 !important;
        color: #444 !important;
        font-weight: 600 !important;
        text-transform: capitalize !important;
        padding: 8px 16px !important;
        box-shadow: none !important;
        border-radius: 10px !important;
        font-size: 0.9rem !important;
    }
    .fc-button-active {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: white !important;
    }
    .fc-button:hover:not(.fc-button-active) {
        background-color: #f8f9fa !important;
    }

    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        color: #333;
    }

    /* ປັບແຕ່ງຕົວເລກວັນທີ */
    .fc-daygrid-day-number {
        padding: 8px 12px !important;
        color: #444;
        font-weight: 500;
        text-decoration: none !important;
    }

    .card-calendar {
        border-radius: 20px !important;
        overflow: hidden;
    }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 p-0 main-content font-lao">
    <?php include '../../includes/navbar.php'; ?>

    <div class="px-4 pb-5">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <div>
                <h2 class="fw-bold text-dark mb-0"><i class="fas fa-calendar-alt text-primary me-2"></i>ປະຕິທິນການຈອງທົວ</h2>
                <p class="text-muted small">ເບິ່ງພາບລວມການເດີນທາງ ແລະ ການຈອງທັງໝົດ</p>
            </div>
            
            <!-- Legend (ຄຳອະທິບາຍສີ) -->
            <div class="d-flex gap-3 bg-white p-2 px-3 rounded-pill shadow-sm border">
                <div class="d-flex align-items-center small">
                    <span class="badge bg-success rounded-circle p-1 me-2" style="width: 10px; height: 10px;"> </span> ຢືນຢັນແລ້ວ
                </div>
                <div class="d-flex align-items-center small">
                    <span class="badge bg-warning rounded-circle p-1 me-2" style="width: 10px; height: 10px;"> </span> ລໍຖ້າອະນຸມັດ
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm card-calendar bg-white">
            <div class="card-body p-4">
                <!-- ບ່ອນວາງປະຕິທິນ -->
                <div id='calendar'></div>
            </div>
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
        locale: 'lo', // ພາສາລາວ
        firstDay: 1, // ໃຫ້ເລີ່ມມື້ວັນຈັນ
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: 'ມື້ນີ້',
            month: 'ເດືອນ',
            week: 'ອາທິດ',
            list: 'ລາຍການ'
        },
        events: 'get_events.php', // ດຶງຂໍ້ມູນ JSON
        eventClick: function(info) {
            if (info.event.url) {
                // ໂຊ Loading ກ່ອນໄປໜ້າ View
                Swal.fire({
                    title: 'ກຳລັງເປີດ...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });
                window.location.href = info.event.url;
                info.jsEvent.preventDefault();
            }
        },
        height: 'auto',
        dayMaxEvents: true, // ຖ້າມີຫຼາຍງານເກີນໄປ ໃຫ້ຂຶ້ນ +more
    });
    calendar.render();
});
</script>

<?php include '../../includes/footer.php'; ?>