<nav class="navbar">
    <div class="container d-flex justify-content-between align-items-center"
        style="display: flex; justify-content: space-between; align-items: center;">
        <a class="navbar-brand" href="../dashboard.php">IntraHub</a>

        <?php $currentUser = current_user(); ?>
        <div class="nav-links">
            <button onclick="openComplaintModal()" class="btn btn-sm" style="
        border-radius: 20px;
        background: rgba(255,193,7,0.15);
        color: #ffc107;
        border: 1px solid rgba(255,193,7,0.4);
        backdrop-filter: blur(6px);
        padding: 6px 14px;
        transition: all 0.3s ease;
    " onmouseover="this.style.background='rgba(255,193,7,0.3)'"
                onmouseout="this.style.background='rgba(255,193,7,0.15)'">
                <i class="fas fa-bullhorn"></i> Şikayet / Öneri
            </button>

            <span class="text-white me-3" style="color: var(--text-main); margin-right: 1rem;">
                Merhaba, <?= e($currentUser['full_name'] ?? $currentUser['username'] ?? 'Kullanıcı') ?>
            </span>

            <?php if (isset($currentUser['role']) && $currentUser['role'] === 'admin'): ?>
                <a href="../index.php" class="btn btn-sm"
                    style="color: var(--secondary-color); margin-right: 10px;">Yönetim</a>
            <?php endif; ?>

            <a href="../logout.php" class="btn btn-sm"
                style="color: var(--text-muted); border: 1px solid var(--border-color); padding: 5px 10px; border-radius: 8px;">
                <i class="fas fa-sign-out-alt"></i> Çıkış
            </a>
        </div>
    </div>
</nav>

<!-- Complaint Modal -->
<style>
    /* Modal Overlay */
    #complaintOverlay {
        background: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(5px);
    }

    /* Modal Card */
    #complaintCard {
        background: rgba(15, 23, 42, 0.85) !important;
        /* Dark Slate */
        backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
        color: #f1f5f9 !important;
    }

    /* Inputs */
    #complaintCard textarea,
    #complaintCard input {
        background: rgba(0, 0, 0, 0.4) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #e2e8f0 !important;
    }

    #complaintCard textarea:focus,
    #complaintCard input:focus {
        background: rgba(0, 0, 0, 0.6) !important;
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
        outline: none !important;
    }

    /* Buttons */
    .btn-glass-cancel {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #94a3b8;
    }

    .btn-glass-cancel:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .btn-glass-submit {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        color: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-glass-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
    }
</style>

<div class="modal-overlay" id="complaintModal"
    style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; z-index: 2000; justify-content: center; align-items: center;"
    id="complaintOverlay">
    <div class="glass-card p-4" id="complaintCard"
        style="width: 100%; max-width: 500px; margin: 2rem; border-radius: 20px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 fw-bold"><i class="fas fa-bullhorn me-2" style="color: #fbbf24;"></i> Şikayet/Öneri Kutusu
            </h5>
            <button type="button" class="btn-close btn-close-white" onclick="closeComplaintModal()"
                aria-label="Close"></button>
        </div>

        <form id="complaintForm">
            <div class="mb-3">
                <label class="form-label text-muted small text-uppercase fw-bold">Mesajınız</label>
                <textarea name="content" class="form-control" rows="5"
                    placeholder="Görüşleriniz veya şikayetleriniz bizim için değerli..." required
                    style="resize: none;"></textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="form-check">
                    <input type="checkbox" name="anonymous" id="anonCheck" class="form-check-input">
                    <label for="anonCheck" class="form-check-label text-sm text-muted">Anonim Gönder</label>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-glass-cancel px-3"
                        onclick="closeComplaintModal()">İptal</button>
                    <button type="submit" class="btn btn-glass-submit px-4"><i class="fas fa-paper-plane me-2"></i>
                        Gönder</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openComplaintModal() {
        document.getElementById('complaintForm').reset();
        const modal = document.getElementById('complaintModal');
        modal.style.display = 'flex';
        // Add class for animation if needed
        document.getElementById('complaintOverlay').style.background = 'rgba(0, 0, 0, 0.6)';
    }

    function closeComplaintModal() {
        document.getElementById('complaintModal').style.display = 'none';
    }

    // Close on click outside
    document.getElementById('complaintModal').addEventListener('click', function (e) {
        if (e.target === this) closeComplaintModal();
    });

    document.getElementById('complaintForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const payload = {
            action: 'submit',
            content: formData.get('content'),
            anonymous: document.getElementById('anonCheck').checked
        };

        fetch('/php/company_Chat/intrahub/submit_complaint.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(r => r.json())
            .then(resp => {
                if (resp.ok) {
                    closeComplaintModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Teşekkürler!',
                        text: 'Mesajınız yönetime iletildi.',
                        background: 'rgba(255, 255, 255, 0.95)',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata',
                        text: resp.error || 'Bir sorun oluştu.',
                        background: 'rgba(255, 255, 255, 0.95)'
                    });
                }
            });
    });
</script>