<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role = in_array($_POST['role'] ?? '', ['admin', 'employee']) ? $_POST['role'] : 'employee';
    $department = trim($_POST['department'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $email === '' || $password === '') {
        flash_set('error', 'Kullanıcı adı, e-posta ve şifre zorunludur.');

    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username,email,password,full_name,role,department) VALUES (:username,:email,:password.:full_name,:role.:department)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'full_name' => $full_name,
                'role' => $role,
                'department' => $department,
            ]);
            $newId = $pdo->lastInsertId();
            flash_set('success', 'Kullanıcı oluşturuldu (ID:' . $newId . ')');


            //opsiyonel hoşgeldin mesajı
            try {
                send_mail($email, 'IntraHub Hesabınız Oluşturuldu', "Merhaba {$full_name},\n\nHesabınız oluşturuldu. Kullanıcı adı: {$username}\n");
            } catch (Exception $e) {
                //mail başarısız göderim  olsa bile kullanıcı oluyşturuldu
                flash_set('info', 'Kullanıcı oluşturuldu ancak e-posta gönderilemedi: ' . $e->getMessage());
            }

            header('Location: /admin/users.php');
            exit;
        } catch (Exception $e) {
            flash_set('error', 'Kullanıcı oluşturulurken hata: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Oluştur-Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="p-4">
    <div class="container">
        <h3>Kullanıcı Oluştur</h3>
        <?php if ($m = flash_get('error')): ?>
            <div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
        <?php if ($m = flash_get('success')): ?>
            <div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
        <?php if ($m = flash_get('info')): ?>
            <div class="alert alert-info"><?= e($m) ?></div><?php endif; ?>

        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Kullanıcı Adı:</label>
                <input name="username" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">E-posta:</label>
                <input name="email" type="email" class="form-control" required>
            </div>
        </form>
    </div>

</body>

</html>