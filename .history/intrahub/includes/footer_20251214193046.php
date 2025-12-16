<script src="/php/company_Chat/intrahub/assets/js/main.js"></script>
<script src="/php/company_Chat/intrahub/assets/js/particles.js"></script>

<script>
    /* ===== FORCE DARK COMPLAINT MODAL ===== */

    #complaintModal {
        background: rgba(0, 0, 0, 0.55)!important;
    }

    /* Kart */
    #complaintModal.glass - card {
        background - color: rgba(18, 18, 28, 0.95)!important;
        backdrop - filter: blur(16px)!important;
        border: 1px solid rgba(255, 255, 255, 0.15)!important;
        border - radius: 18px!important;
        color: #e5e7eb!important;
    }

    /* TEXTAREA - BEYAZI ÖLDÜRÜR */
    #complaintModal textarea,
        #complaintModal textarea.form - control {
        background - color: rgba(0, 0, 0, 0.55)!important;
        color: #e5e7eb!important;
        border: 1px solid rgba(255, 255, 255, 0.2)!important;
        box - shadow: none!important;
        outline: none!important;
        appearance: none!important;
        -webkit - appearance: none!important;
    }

    /* Placeholder */
    #complaintModal textarea::placeholder {
        color: rgba(255, 255, 255, 0.45)!important;
    }

    /* Focus */
    #complaintModal textarea:focus {
        background - color: rgba(0, 0, 0, 0.65)!important;
        border - color: #6366f1!important;
    }

    /* Checkbox */
    #complaintModal input[type = "checkbox"] {
        background - color: rgba(255, 255, 255, 0.15)!important;
        border: 1px solid rgba(255, 255, 255, 0.3)!important;
    }

    #complaintModal input[type = "checkbox"]:checked {
        background - color: #6366f1!important;
        border - color: #6366f1!important;
    }

    /* Label */
    #complaintModal label {
        color: #9ca3af!important;
    }

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