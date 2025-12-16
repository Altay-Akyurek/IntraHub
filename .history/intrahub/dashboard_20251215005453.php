<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php'; // Ensure functions are loaded

require_login();
$user = current_user();

$pageTitle = 'Panel';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container mt-4 fade-in-up">
    <div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <!-- Messages Card -->
        <div class="glass-card">
            <h3><i class="fas fa-envelope" style="color: var(--primary-color)"></i> Mesajlar</h3>
            <p class="text-muted mt-3">Gelen kutunuza göz atın ve ekip arkadaşlarınızla iletişim kurun.</p>
            <a href="messages/index.php" class="btn btn-primary mt-3"
                style="display: inline-block; width: auto;">Mesajlara Git</a>
        </div>

        <!-- Events Card -->
        <div class="glass-card">
            <h3><i class="fas fa-calendar" style="color: var(--secondary-color)"></i> Etkinlikler</h3>
            <p class="text-muted mt-3">Yaklaşan şirket etkinliklerini ve toplantıları görüntüleyin.</p>
            <a href="events/index.php" class="btn btn-primary mt-3"
                style="display: inline-block; width: auto; background: linear-gradient(135deg, var(--secondary-color), #f472b6);">Takvimi
                Aç</a>
        </div>

        <!-- Pulses Card -->
        <div class="glass-card">
            <h3><i class="fas fa-heartbeat" style="color: #10b981"></i> Nabızlar</h3>
            <p class="text-muted mt-3">Şirket içi anketler ve durum güncellemeleri.</p>
            <a href="pulses/index.php" class="btn btn-primary mt-3"
                style="display: inline-block; width: auto; background: linear-gradient(135deg, #10b981, #34d399);">Görüntüle</a>
        </div>

        <?php if ($user['role'] === 'admin'): ?>
            <!-- Admin Card -->
            <div class="glass-card">
                <h3><i class="fas fa-user-shield" style="color: #f59e0b"></i> Yönetim</h3>
                <p class="text-muted mt-3">Kullanıcıları ve sistem ayarlarını yönetin.</p>
                <a href="admin/index.php" class="btn btn-primary mt-3"
                    style="display: inline-block; width: auto; background: linear-gradient(135deg, #f59e0b, #fbbf24);">Panele
                    Git</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>