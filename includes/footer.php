</div> <!-- ປິດ row ຈາກ header -->
</div> <!-- ປິດ container-fluid ຈາກ header -->

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ==========================================
// 1. ລະບົບແຈ້ງເຕືອນ Toast (ມຸມຂວາເທິງ)
// ==========================================
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

// ກວດເຊັກ Parameter "msg" ຈາກ URL ເພື່ອໂຊ Pop-up ອັດຕະໂນມັດ
const urlParams = new URLSearchParams(window.location.search);
const msg = urlParams.get('msg');

if (msg === 'success') {
    Toast.fire({ icon: 'success', title: 'ບັນທຶກຂໍ້ມູນສຳເລັດແລ້ວ!' });
} else if (msg === 'updated') {
    Toast.fire({ icon: 'success', title: 'ອັບເດດຂໍ້ມູນສຳເລັດແລ້ວ!' });
} else if (msg === 'deleted') {
    Toast.fire({ icon: 'info', title: 'ລຶບຂໍ້ມູນຮຽບຮ້ອຍແລ້ວ!' });
} else if (msg === 'error') {
    Swal.fire({ icon: 'error', title: 'ເກີດຂໍ້ຜິດພາດ!', text: 'ກະລຸນາກວດສອບຂໍ້ມູນຄືນໃໝ່', confirmButtonColor: '#0d6efd' });
} else if (msg === 'duplicate') {
    Swal.fire({ icon: 'warning', title: 'ຂໍ້ມູນຊ້ຳກັນ!', text: 'ລະຫັດນີ້ມີໃນລະບົບແລ້ວ', confirmButtonColor: '#f6c23e' });
}

// ==========================================
// 2. ຟັງຊັນຢືນຢັນການຈັດການ (Pop-up ກາງຈໍ)
// ==========================================

// ຟັງຊັນລວມສຳລັບການ Redirect ໄປຫາ URL ພ້ອມ ID
function navigateTo(id, url) {
    const separator = url.includes('?') ? '&' : '?';
    window.location.href = url + separator + "id=" + id;
}

// ຢືນຢັນການອອກຈາກລະບົບ
function confirmLogout() {
    Swal.fire({
        title: 'ອອກຈາກລະບົບ?',
        text: "ທ່ານຕ້ອງການອອກຈາກລະບົບແທ້ບໍ່?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-sign-out-alt me-1"></i> ອອກຈາກລະບົບ',
        cancelButtonText: 'ຍົກເລີກ',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo BASE_URL; ?>logout.php';
        }
    })
}

// ຢືນຢັນການລຶບ
function confirmDelete(id, url) {
    Swal.fire({
        title: 'ຢືນຢັນການລຶບ?',
        text: "ຂໍ້ມູນນີ້ຈະຖືກລຶບອອກຈາກລະບົບຖາວອນ!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ລຶບທັນທີ',
        cancelButtonText: 'ຍົກເລີກ',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            navigateTo(id, url);
        }
    })
}

// ຢືນຢັນການອະນຸມັດ
function confirmApprove(id, url) {
    Swal.fire({
        title: 'ອະນຸມັດການຈອງ?',
        text: "ລະບົບຈະຢືນຢັນການຈອງ ແລະ ສົ່ງ Email ຫາລູກຄ້າ",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ອະນຸມັດທັນທີ',
        cancelButtonText: 'ປິດ',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'ກຳລັງດຳເນີນການ...',
                text: 'ລະບົບກຳລັງສົ່ງ Email ໃບຢັ້ງຢືນ, ກະລຸນາລໍຖ້າຈັກຄູ່',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading() }
            });
            navigateTo(id, url);
        }
    })
}

// ຢືນຢັນການຍົກເລີກ
function confirmCancel(id, url) {
    Swal.fire({
        title: 'ຍົກເລີກການຈອງ?',
        text: "ທ່ານແນ່ໃຈບໍ່ວ່າຈະຍົກເລີກລາຍການນີ້?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ຍົກເລີກລາຍການ',
        cancelButtonText: 'ປິດ',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            navigateTo(id, url);
        }
    })
}
</script>
</body>
</html>