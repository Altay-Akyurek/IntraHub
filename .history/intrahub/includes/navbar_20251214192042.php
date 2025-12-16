<nav class="navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="../dashboard.php">IntraHub</a>

        <?php $currentUser = current_user(); ?>
        <div class="nav-links d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-warning" onclick="openComplaintModal()" style="border-radius: 20px;">
                <i class="fas fa-bullhorn"></i> Şikayet / Öneri
            </button>

            <span class="text-white me-2">
                Merhaba, <?= e($currentUser['full_name'] ?? $currentUser['username'] ?? 'Kullanıcı') ?>
            </span>

            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                <a href="admin/index.php" class="btn btn-sm" style="color: var(--secondary-color);">
                    Yönetim
                </a>
            <?php endif; ?>

            <a href="../logout.php" class="btn btn-sm"
                style="color: var(--text-muted); border: 1px solid var(--border-color); border-radius: 8px;">
                <i class="fas fa-sign-out-alt"></i> Çıkış
            </a>
        </div>
    </div>
</nav>

<!-- ===== Complaint Modal ===== -->
<div id="complaintModal" class="modal-overlay">
    <div class="glass-card complaint-modal">
        <h5>Şikayet / Öneri Kutusu</h5>

        <form id="complaintForm">
            <div class="mb-3">
                <textarea name="content" class="form-control" rows="4" placeholder="Görüşleriniz bizim için değerli..."
                    required></textarea>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="anonCheck">
                <label class="form-check-label" for="anonCheck">
                    Anonim olarak gönder
                </label>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="closeComplaintModal()">İptal</button>
                <button type="submit" class="btn btn-warning">Gönder</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== STYLES ===== -->
<style>
    /* Overlay */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 2000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Modal Glass */
    .complaint-modal {
        width: 100%;
        max-width: 480px;
        padding: 1.6rem;
        background: rgba(20, 20, 30, 0.9);
        backdrop-filter: blur(14px);
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.7);
        color: var(--text-main);
    }

    /* Title */
    .complaint-modal h5 {
        margin-bottom: 1rem;
        font-weight: 600;
    }

    /* Inputs */
    .complaint-modal .form-control {
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        color: var(--text-main);
    }

    .complaint-modal .form-control::placeholder {
        color: rgba(255, 255, 255, 0.45);
    }

    .complaint-modal .form-control:focus {
        background: rgba(0, 0, 0, 0.5);
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
        color: var(--text-main);
    }

    /* Checkbox */
    .complaint-modal .form-check-input {
        background-color: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .complaint-modal .form-check-input:checked {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }

    /* Buttons */
    .complaint-modal .btn-warning {
        border-radius: 30px;
        padding: 0.45rem 1.5rem;
        box-shadow: 0 0 18px rgba(251, 191, 36, 0.4);
    }

    .complaint-modal .btn-secondary {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: var(--text-muted);
    }
</style>

<!-- ===== SCRIPT ===== -->
<script>
    function openComplaintModal() {
        document.getElementById('complaintForm').reset();
        document.getElementById('complaintModal').style.display = 'flex';
    }

    function closeComplaintModal() {
        document.getElementById('complaintModal').style.display = 'none';
    }

    document.getElementById('complaintForm').addEventListener('submit', function (e) {
        e.preventDefault();

        fetch('/php/company_Chat/intrahub/submit_complaint.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                content: this.content.value,
                anonymous: document.getElementById('anonCheck').checked
            })
        })
            .then(r => r.json())
            .then(resp => {
                if (resp.ok) {
                    alert('Teşekkürler! Mesajınız iletildi.');
                    closeComplaintModal();
                } else {
                    alert('Hata: ' + resp.error);
                }
            });
    });
</script>