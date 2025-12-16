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
        $success = "Hesabınız oluşturuldu. Giriş yapabilirsiniz.";

        // Opsiyonel: hoşgeldin e-postası (hata mail başarısızlığını gösterme)
        try {
            send_mail($email, 'IntraHub - Hoşgeldiniz', "Merhaba {$full_name},\n\nIntraHub hesabınız oluşturuldu. Kullanıcı adı: {$username}");
        } catch (Exception $e) {
            // sesssion flash yerine local mesaj
            $errors[] = "Hesap oluşturuldu ancak e-posta gönderilemedi.";
        }
    }
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kayıt - IntraHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Yeni Hesap Oluştur</h4>

                        <?php if ($errors): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $e): ?>
                                    <div><?= e($e) ?></div><?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= e($success) ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Kullanıcı adı</label>
                                <input name="username" class="form-control" required
                                    value="<?= e($_POST['username'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">E-posta</label>
                                <input name="email" type="email" class="form-control" required
                                    value="<?= e($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">İsim Soyisim</label>
                                <input name="full_name" class="form-control"
                                    value="<?= e($_POST['full_name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Şifre</label>
                                <input name="password" type="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Şifre (Tekrar)</label>
                                <input name="password_confirm" type="password" class="form-control" required>
                            </div>
                            <button class="btn btn-primary w-100">Kayıt Ol</button>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="../intrahub/index.php">Giriş Yap</a> | <a
                                href="../intrahub/forgot_password.php">Şifremi
                                Unuttum</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>