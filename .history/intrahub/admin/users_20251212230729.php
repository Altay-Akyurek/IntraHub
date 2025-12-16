<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php'; // <- e() ve flash helpers için gerekli

// Basit kullanıcı listesi
$stmt = $pdo->query("SELECT id, username, full_name, email, role, department FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kullanıcı Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <div class="container">
        <h3>Kullanıcılar</h3>

        <?php if ($msg = flash_get('success')): ?>
            <div class="alert alert-success"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash_get('error')): ?>
            <div class="alert alert-danger"><?= e($msg) ?></div>
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kullanıcı</th>
                    <th>İsim</th>
                    <th>E-posta</th>
                    <th>Rol</th>
                    <th>Departman</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= e($u['id']) ?></td>
                        <td><?= e($u['username']) ?></td>
                        <td><?= e($u['full_name']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e($u['role']) ?></td>
                        <td><?= e($u['department']) ?></td>
                        <td>
                            <a href="/admin/edit_user.php?id=<?= e($u['id']) ?>" class="btn btn-sm btn-primary">Düzenle</a>
                            <a href="/admin/delete_user.php?id=<?= e($u['id']) ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Kullanıcıyı silmek istediğinize emin misiniz?')">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>