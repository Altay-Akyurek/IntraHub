<script src="/php/company_Chat/intrahub/assets/js/main.js"></script>
<script src="/php/company_Chat/intrahub/assets/js/particles.js"></script>

<script>
    // Global SweetAlert2 Configuration
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: 'rgba(255, 255, 255, 0.9)',
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Handle PHP Flash Messages
    <?php if ($msg = flash_get('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Başarılı!',
            text: '<?= e($msg) ?>',
            background: 'rgba(255, 255, 255, 0.95)',
            confirmButtonColor: '#34d399',
            confirmButtonText: 'Tamam'
        });
    <?php endif; ?>

    <?php if ($msg = flash_get('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Hata!',
            text: '<?= e($msg) ?>',
            background: 'rgba(255, 255, 255, 0.95)',
            confirmButtonColor: '#f87171',
            confirmButtonText: 'Tamam'
        });
    <?php endif; ?>

    // Global Delete Confirmation Helper
    function confirmDelete(e, url) {
        e.preventDefault();
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu işlemi geri alamazsınız!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Evet, Sil!',
            cancelButtonText: 'İptal',
            background: 'rgba(255, 255, 255, 0.95)'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
        return false;
    }
</script>
</body>
</html>
