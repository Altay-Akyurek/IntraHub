<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/vendor/autoload.php';

// Eğer kullanıcı giriş yaptıysa direkt dashboard’a gider
if (is_logged_in()) {
    header('Location: ../intrahub/dashboard.php');
    exit;
}

// Form POST edildiyse login kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($username, $password)) {
        header('Location: ../dashboard.php');
        exit;
    } else {
        flash_set('error', 'Kullanıcı adı veya şifre hatalı.');
    }
}

$pageTitle = 'Giriş Yap';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-container fade-in-up">
    <div class="glass-card">
        <div class="text-center mb-3">
            <h2 class="navbar-brand" style="float: none; font-size: 2.5rem;">IntraHub</h2>
            <p class="text-muted" style="color: var(--text-muted)">Hesabınıza giriş yapın</p>
        </div>

        <?php if ($err = flash_get('error')): ?>
            <div class="alert">
                <i class="fas fa-exclamation-circle"></i> <?= e($err) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Kullanıcı Adı veya E-posta</label>
                <input name="username" class="form-control" required placeholder="ör. kullanici_adi">
            </div>
            <div class="mb-3">
                <label class="form-label">Şifre</label>
                <input name="password" type="password" class="form-control" required placeholder="••••••••">
            </div>
            <button class="btn btn-primary w-100">
                Giriş Yap <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div class="mt-4 text-center">
            <p style="font-size: 0.9rem; color: var(--text-muted);">
                Hesabınız yok mu? <a href="register.php">Hemen Kayıt Olun</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>