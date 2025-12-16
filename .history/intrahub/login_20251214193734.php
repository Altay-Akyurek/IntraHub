<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    header('Location: /dashboard.php');
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['u'] ?? '');
    $p = $_POST['p'] ?? '';
    if (login($u, $p)) {
        header('Location:../intrahub/dashboard.php');
        exit;
    } else {
        $error = 'Kullanıcı adı/e-posta veya şifre hatalı';
    }
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Giriş - IntraHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Giriş</h4>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Kullanıcı adı veya e-posta</label>
                                <input name="u" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Şifre</label>
                                <input name="p" type="password" class="form-control" required>
                            </div>
                            <button class="btn btn-primary w-100">Giriş</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="/register.php">Kayıt Ol</a> | <a href="/forgot_password.php">Şifremi Unuttum</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>