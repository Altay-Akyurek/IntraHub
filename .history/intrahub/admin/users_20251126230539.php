<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';

//Basit kullanıcı listesi
$stmt = $pdo->query("SELECT id, username, full_name,email,role,department FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUllanıcı Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <div class="container">
        <h3>Kullanıcılar</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kullanıcı</th>
                    <th>İsim</th>
                    <th>E-posta</th>
                    <th>Rol</th>
                    <th>Departman</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u):?>
                    <tr>
                        <td><?=e($u['id'])?></td>
                    </tr>
            </tbody>
        </table>
    </div>

</body>

</html>