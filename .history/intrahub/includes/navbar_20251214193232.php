<nav class="navbar">
    <div class="container d-flex justify-content-between align-items-center"
        style="display: flex; justify-content: space-between; align-items: center;">
        <a class="navbar-brand" href="../dashboard.php">IntraHub</a>

        <?php $currentUser = current_user(); ?>
        <div class="nav-links">
            <button class="btn btn-sm btn-outline-warning" onclick="openComplaintModal()" style="border-radius: 20px;">
                <i class="fas fa-bullhorn"></i> Şikayet/Öneri
            </button>
            <span class="text-white me-3" style="color: var(--text-main); margin-right: 1rem;">
                Merhaba, <?= e($currentUser['full_name'] ?? $currentUser['username'] ?? 'Kullanıcı') ?>
            </span>

            <?php if (isset($currentUser['role']) && $currentUser['role'] === 'admin'): ?>
                <a href="admin/index.php" class="btn btn-sm"
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
<div class="modal-overlay" id="complaintModal"
    style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div class="glass-card" style="width: 100%; max-width: 500px; margin: 2rem;">
        <h5 class="mb-3">Şikayet veya Öneri Kutusu</h5>
        <form id="complaintForm">
            <div class="mb-3">
                <textarea name="content" class="form-control" rows="4" placeholder="Görüşleriniz bizim için değerli..."
                    required style="background: rgba(0,0,0,0.2);"></textarea>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="anonymous" id="anonCheck" class="form-check-input">
                <label for="anonCheck" class="form-check-label">Anonim olarak gönder</label>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary"
                    onclick="document.getElementById('complaintModal').style.display='none'">İptal</button>
                <button type="submit" class="btn btn-warning">Gönder</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openComplaintModal() {
        document.getElementById('complaintForm').reset();
        document.getElementById('complaintModal').style.display = 'flex';
    }

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
                    alert('Teşekkürler! Mesajınız iletildi.');
                    document.getElementById('complaintModal').style.display = 'none';
                } else {
                    alert('Hata: ' + resp.error);
                }
            });
    });
</script>