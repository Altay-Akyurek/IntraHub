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
        $sql = "INSERT INTO users 
        (username, email, password, full_name, role, department) 
        VALUES 
        (:username, :email, :password, :full_name, :role, :department)";
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

            // ... PHP Code part ... (Keep the part before HTML)
            header('Location: users.php'); // Fixed redirect path
            exit;
        } catch (Exception $e) {
            flash_set('error', 'Kullanıcı oluşturulurken hata: ' . $e->getMessage());
        }
    }
}

$pageTitle = 'Yeni Kullanıcı Oluştur';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container mt-4 fade-in-up" style="max-width: 800px;">
    <div class="glass-card">
        <h4 class="mb-4"><i class="fas fa-user-plus" style="color: var(--primary-color)"></i> Yeni Kullanıcı Oluştur
        </h4>

        <?php if ($m = flash_get('error')): ?>
            <div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
        <?php if ($m = flash_get('success')): ?>
            <div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
        <?php if ($m = flash_get('info')): ?>
            <div class="alert alert-info"><?= e($m) ?></div><?php endif; ?>

        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Kullanıcı Adı:</label>
                <input name="username" class="form-control" required style="background: rgba(0,0,0,0.2);">
            </div>
            <div class="col-md-6">
                <label class="form-label">E-posta:</label>
                <input name="email" type="email" class="form-control" required style="background: rgba(0,0,0,0.2);">
            </div>
            <div class="col-md-6">
                <label class="form-label">İsim Soyisim</label>
                <input name="full_name" class="form-control" style="background: rgba(0,0,0,0.2);">
            </div>
            <div class="col-md-3">
                <label class="form-label">Rol</label>
                <select name="role" class="form-select form-control"
                    style="background: rgba(0,0,0,0.2); color: var(--text-main);">
                    <option value="employee">Çalışan</option>
                    <option value="admin">Yönetici</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Departman</label>
                <input name="department" class="form-control" style="background: rgba(0,0,0,0.2);">
            </div>
            <div class="col-md-6">
                <label class="form-label">Şifre:</label>
                <input name="password" type="password" class="form-control" style="background: rgba(0,0,0,0.2);">
            </div>
            <div class="col-12 mt-4">
                <button class="btn btn-primary" style="width: auto; padding: 0.5rem 2rem;">Kaydet</button>
                <a href="users.php" class="btn" style="color: var(--text-muted); margin-left: 10px;">İptal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>