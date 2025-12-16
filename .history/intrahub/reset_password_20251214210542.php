<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$token = $_GET['token'] ?? ($_POST['token'] ?? null);
$errors = [];
$success = null;
$validReset = null;

if (!$token) {
    $errors[] = 'Geçersiz veya eksik token.';
} else {
    // token doğrulama
    $stmt = $pdo->
        prepare("SELECT pr.*, u.email, u.full_name, u.id as user_id FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE pr.token = :t LIMIT 1");
    $stmt->execute(['t' => $token]);
    $row = $stmt->fetch();
    if (!$row) {
        $errors[] = 'Token bulunamadı.';
    } else {
        $now = new DateTime();
        $expires = new DateTime($row['expires_at']);
        if ($now > $expires) {
            $errors[] = 'Token süresi dolmuş.';
        } else {
            $validReset = $row;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validReset) {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    if ($password === '' || $password !== $password_confirm) {
        $errors[] = 'Şifre boş olamaz ve şifreler eşleşmelidir.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $pdo->prepare("UPDATE users SET password = :p WHERE id = :id");
        $upd->execute(['p' => $hash, 'id' => $validReset['user_id']]);

        // Kullanılan token'i sil
        $del = $pdo->prepare("DELETE FROM password_resets WHERE id = :id");
        $del->execute(['id' => $validReset['id']]);

        $success = 'Parolanız başarıyla değiştirildi. Giriş yapabilirsiniz.';
    }
}


$pageTitle = 'Parola Sıfırla';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-container fade-in-up" style="max-width: 500px;">
    <div class="glass-card">
        <div class="text-center mb-3">
            <h2 class="navbar-brand" style="float: none; font-size: 2.5rem;">IntraHub</h2>
            <p class="text-muted" style="color: var(--text-muted)">Parola Sıfırlama</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert">
                <?php foreach ($errors as $e): ?>
                    <div><i class="fas fa-exclamation-triangle"></i> <?= e($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert"
                style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3); color: #6ee7b7;">
                <i class="fas fa-check-circle"></i> <?= e($success) ?>
            </div>
            <div class="mt-4 text-center">
                <a href="index.php" class="btn btn-primary w-100">Giriş Yap</a>
            </div>
        <?php endif; ?>

        <?php if (!$success && $validReset): ?>
            <form method="post">
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <div class="mb-3">
                    <label class="form-label">Yeni Şifre</label>
                    <input name="password" type="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Yeni Şifre (Tekrar)</label>
                    <input name="password_confirm" type="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button class="btn btn-primary w-100">
                    Parolayı Değiştir <i class="fas fa-check" style="margin-left: 8px;"></i>
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>