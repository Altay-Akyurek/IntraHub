<?php
// Tek seferlik admin oluşturucu.
// Güvenlik: Oluşturduktan sonra bu dosyayı silin veya erişimi kısıtlayın.
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
session_start();

// Eğer veritabanında admin varsa bu aracı devre dışı bırak
$stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM users WHERE role = 'admin'");
$stmt->execute();
$row = $stmt->fetch();
if ($row && $row['cnt'] > 0) {
    echo "<h3>Zaten en az bir admin var. Bu sayfayı kullanamazsınız.</h3>";
    echo '<p>Mevcut adminleri görmek için <a href="/admin/users.php">/admin/users.php</a> kullanın.</p>';
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

    // Benzersiz kontrol
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
    $stmt->execute(['u' => $username, 'e' => $email]);
    if ($stmt->fetch()) {
        $errors[] = 'Kullanıcı adı veya e-posta zaten kullanılıyor.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (:username,:email,:password,:full_name,'admin')");
        $ins->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hash,
            'full_name' => $full_name
        ]);
        $success = "Admin hesabı oluşturuldu. Kullanıcı adı: {$username}";
        // Güvenlik önerisi: dosyayı silin
        $success .= "<br>Lütfen bu dosyayı sunucudan silin veya erişimi kapatın.";
    }
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>IntraHub - İlk Admin Oluştur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <div class="container">
        <h3>İlk Admin Hesabı Oluştur</h3>
        <?php if ($errors): ?>
            <div class="alert alert-danger"><?= e(implode('<br>', $errors)) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form method="post" class="row g-3" style="max-width:600px">
                <div class="col-md-6">
                    <label class="form-label">Kullanıcı adı</label>
                    <input name="username" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">E-posta</label>
                    <input name="email" type="email" class="form-control" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">İsim Soyisim</label>
                    <input name="full_name" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Şifre</label>
                    <input name="password" type="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Şifre (Tekrar)</label>
                    <input name="password_confirm" type="password" class="form-control" required>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">Admin Oluştur</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>