<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/mailer.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Eğer kullanıcı zaten girişliyse dashboard'a yönlendir
if (isset($_SESSION['user'])) {
    header('Location: ../intrahub/dashboard.php');
    exit;
}

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $errors[] = 'Kullanıcı adı, e-posta ve şifre zorunludur.';
    }

    if ($password !== $password_confirm) {
        $errors[] = 'Şifreler eşleşmiyor.';
    }

// E-posta ve kullanıcı adı benzersiz mi?
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
        $stmt->execute(['u' => $username, 'e' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Kullanıcı adı veya e-posta zaten kullanılıyor.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, full_name, role) VALUES (:username,:email,:password,:full_name,'employee')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hash,
            'full_name' => $full_name
        ]);
        $newId = $pdo->lastInsertId();
        
        flash_set('success', "Hesabınız başarıyla oluşturuldu. Giriş yapabilirsiniz.");
        
        // Opsiyonel: hoşgeldin e-postası (hata mail başarısızlığını gösterme)
        try {
            send_mail($email, 'IntraHub - Hoşgeldiniz', "Merhaba {$full_name},\n\nIntraHub hesabınız oluşturuldu. Kullanıcı adı: {$username}");
        } catch (Exception $e) {
             // sesssion flash yerine local mesaj - Log it?
        }
        
        header('Location: index.php');
        exit;
    }
}

$pageTitle = 'Kayıt Ol';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-container fade-in-up" style="max-width: 500px;">
    <div class="glass-card">
        <div class="text-center mb-3">
            <h2 class="navbar-brand" style="float: none; font-size: 2.5rem;">IntraHub</h2>
            <p class="text-muted" style="color: var(--text-muted)">Aramıza Katılın</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert">
                <?php foreach ($errors as $e): ?>
                    <div><i class="fas fa-exclamation-triangle"></i> <?= e($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Kullanıcı adı</label>
                <input name="username" class="form-control" required placeholder="kullanici123"
                    value="<?= e($_POST['username'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">E-posta</label>
                <input name="email" type="email" class="form-control" required placeholder="ornek@sirket.com"
                    value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">İsim Soyisim</label>
                <input name="full_name" class="form-control" placeholder="Ad Soyad"
                    value="<?= e($_POST['full_name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Şifre</label>
                <input name="password" type="password" class="form-control" required placeholder="••••••••">
            </div>
            <div class="mb-3">
                <label class="form-label">Şifre (Tekrar)</label>
                <input name="password_confirm" type="password" class="form-control" required placeholder="••••••••">
            </div>
            <button class="btn btn-primary w-100">
                Hesap Oluştur <i class="fas fa-user-plus" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div class="mt-4 text-center">
             <p style="font-size: 0.9rem; color: var(--text-muted);">
                Zaten hesabınız var mı? <a href="index.php">Giriş Yap</a>
            </p>
            <div style="margin-top: 10px;">
                <a href="forgot_password.php" style="font-size: 0.85rem; color: var(--text-muted);">Şifremi Unuttum</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>