<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Yönetim Paneli';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container mt-4 fade-in-up">
    <h3 class="mb-4"><i class="fas fa-shield-alt" style="color: #f59e0b"></i> Yönetim Paneli</h3>
    
    <div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        
        <!-- User Management -->
        <div class="glass-card">
            <h4><i class="fas fa-users" style="color: var(--primary-color)"></i> Kullanıcılar</h4>
            <p class="text-muted mt-2">Çalışanları ekleyin, düzenleyin veya silin.</p>
            <div class="d-flex gap-2 mt-3">
                <a href="users.php" class="btn btn-primary btn-sm">Listele</a>
                <a href="create_user.php" class="btn btn-sm" style="border: 1px solid var(--border-color); color: var(--text-main);">Ekle</a>
            </div>
        </div>

        <!-- Complaints Management -->
        <div class="glass-card">
            <h4><i class="fas fa-exclamation-triangle" style="color: var(--secondary-color)"></i> Şikayet Yönetimi</h4>
            <p class="text-muted mt-2">Kullanıcı şikayetlerini ve önerilerini inceleyin.</p>
            <div class="d-flex gap-2 mt-3">
                <a href="complaints.php" class="btn btn-primary btn-sm">İncele</a>
            </div>
        </div>

        <!-- Pulse Management -->
        <div class="glass-card">
            <h4><i class="fas fa-tasks" style="color: #8b5cf6"></i> Pulse Yönetimi</h4>
            <p class="text-muted mt-2">Şirket içi anketleri oluşturun ve yönetin.</p>
            <div class="d-flex gap-2 mt-3">
                <a href="pulses.php" class="btn btn-primary btn-sm">Yönet</a>
            </div>
        </div>

        <!-- System Status (Placeholder) -->
        <div class="glass-card">
            <h4><i class="fas fa-server" style="color: #10b981"></i> Sistem Durumu</h4>
            <p class="text-muted mt-2">Uygulama genel durumu ve loglar.</p>
            <div class="mt-3">
                <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #6ee7b7;">Sistem Çalışıyor</span>
                <span class="badge" style="background: rgba(255, 255, 255, 0.1); color: var(--text-muted);">v1.0.0</span>
            </div>
        </div>
        
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
