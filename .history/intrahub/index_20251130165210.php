<?php
session_start();
require_once "includes/functions.php";

require_once __DIR__ . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['u'] ?? '';
    $p = $_POST['p'] ?? '';

    if (login($u, $p)) {
        header('Location: dashboard.php');
        exit;
    } else {
        flash_set('error', 'Kullanıcı adı/e-posta veya şifre hatası');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>İntrahub Giriş</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">IntraHub Giriş</h4>
                        <?php if ($err = flash_get('error')): ?>
                            <div class="alert alert-danger"><?= e($err) ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Kullanıcı Adı Veya E-posta</label>
                                <input name="u" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Şifre</label>
                                <input name="p" type="password" class="form-control">
                            </div>
                            <button class="btn btn-primary w-100">Giriş</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>