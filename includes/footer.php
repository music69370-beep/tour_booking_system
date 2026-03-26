</div> <!-- ປິດ row ຈາກ header -->
</div> <!-- ປິດ container-fluid ຈາກ header -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// 1. ຕັ້ງຄ່າ Toast (ແຈ້ງເຕືອນມຸມຂວາເທິງ)
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});

// 2. ກວດເຊັກ Parameter "msg" ຈາກ URL
const urlParams = new URLSearchParams(window.location.search);
const msg = urlParams.get('msg');

if (msg === 'success') {
    Toast.fire({ icon: 'success', title: 'ບັນທຶກຂໍ້ມູນສຳເລັດແລ້ວ!' });
} else if (msg === 'updated') {
    Toast.fire({ icon: 'success', title: 'ອັບເດດຂໍ້ມູນສຳເລັດແລ້ວ!' });
} else if (msg === 'deleted') {
    Toast.fire({ icon: 'info', title: 'ລຶບຂໍ້ມູນຮຽບຮ້ອຍແລ້ວ!' });
}

// 3. ຟັງຊັນຢືນຢັນການລຶບ (Pop-up ກາງຈໍ)
function confirmDelete(id, url) {
    Swal.fire({
        title: 'ຢືນຢັນການລຶບ?',
        text: "ທ່ານແນ່ໃຈບໍ່ວ່າຈະລຶບຂໍ້ມູນນີ້? ເມື່ອລຶບແລ້ວບໍ່ສາມາດກູ້ຄືນໄດ້!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ລຶບທັນທີ',
        cancelButtonText: 'ຍົກເລີກ',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // ສົ່ງໄປໄຟລ໌ລຶບ
            window.location.href = url + "?id=" + id;
        }
    })
}
</script>
</body>
</html>