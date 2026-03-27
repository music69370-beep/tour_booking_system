</div> <!-- ປິດ row ຈາກ header -->
</div> <!-- ປິດ container-fluid ຈາກ header -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});

const urlParams = new URLSearchParams(window.location.search);
const msg = urlParams.get('msg');
if (msg === 'success') Toast.fire({ icon: 'success', title: 'ບັນທຶກຂໍ້ມູນສຳເລັດ!' });
else if (msg === 'updated') Toast.fire({ icon: 'success', title: 'ອັບເດດຂໍ້ມູນສຳເລັດ!' });
else if (msg === 'deleted') Toast.fire({ icon: 'info', title: 'ລຶບຂໍ້ມູນຮຽບຮ້ອຍແລ້ວ!' });

// ຟັງຊັນລວມສຳລັບສົ່ງໄປຫາ URL
function navigateTo(id, url) {
    // ຖ້າ url ມີ ? ຢູ່ແລ້ວ ໃຫ້ໃຊ້ &id=, ຖ້າບໍ່ມີໃຫ້ໃຊ້ ?id=
    const separator = url.includes('?') ? '&' : '?';
    window.location.href = url + separator + "id=" + id;
}

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

function confirmApprove(id, url) {
    Swal.fire({
        title: 'ຢືນຢັນການອະນຸມັດ?',
        text: "ລະບົບຈະອັບເດດສະຖານະ ແລະ ສົ່ງ Email ໃບຢັ້ງຢືນຫາລູກຄ້າ",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ອະນຸມັດ ແລະ ສົ່ງ Email',
        cancelButtonText: 'ຍົກເລີກ',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // ໂຊ Loading ບອກໃຫ້ແອດມິນລໍຖ້າ
            Swal.fire({
                title: 'ກຳລັງດຳເນີນການ...',
                text: 'ກະລຸນາລໍຖ້າຈັກຄູ່ ລະບົບກຳລັງສົ່ງ Email ຫາລູກຄ້າ',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            navigateTo(id, url);
        }
    })
}

function confirmCancel(id, url) {
    Swal.fire({
        title: 'ຢືນຢັນການຍົກເລີກ?',
        text: "ທ່ານຕ້ອງການຍົກເລີກລາຍການຈອງນີ້ແທ້ບໍ່?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ຍົກເລີກລາຍການ',
        cancelButtonText: 'ປິດ'
    }).then((result) => {
        if (result.isConfirmed) {
            navigateTo(id, url);
        }
    })
}
</script>
</body>
</html>