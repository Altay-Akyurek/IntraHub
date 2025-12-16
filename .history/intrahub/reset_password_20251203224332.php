<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
session_start();

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
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Parola Sıfırla - IntraHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Parola Sıfırlama</h4>

                        <?php if ($errors): ?>
                            <div class="alert alert-danger"><?= e(implode('<br>', $errors)) ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= e($success) ?></div>
                            <div class="mt-2"><a href="/index.php" class="btn btn-primary">Giriş Yap</a></div>
                        <?php endif; ?>

                        <?php if (!$success && $validReset): ?>
                            <form method="post">
                                <input type="hidden" name="token" value="<?= e($token) ?>">
                                <div class="mb-3">
                                    <label class="form-label">Yeni Şifre</label>
                                    <input name="password" type="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Yeni Şifre (Tekrar)</label>
                                    <input name="password_confirm" type="password" class="form-control" required>
                                </div>
                                <button class="btn btn-primary w-100">Parolayı Değiştir</button>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>