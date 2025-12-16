<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: users.php');
    exit;
}

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$u = $stmt->fetch();

if (!$u) {
    flash_set('error', 'Kullanıcı bulunamadı.');
    header('Location: users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role = in_array($_POST['role'] ?? '', ['admin', 'employee']) ? $_POST['role'] : 'employee';
    $department = trim($_POST['department'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') {
        flash_set('error', 'E-posta zorunludur.');
    } else {
        $sql = "UPDATE users SET email = :email, full_name = :full_name, role = :role, department = :department";
        $params = [
            'email' => $email,
            'full_name' => $full_name,
            'role' => $role,
            'department' => $department,
            'id' => $id
        ];

        if ($password !== '') {
            $sql .= ", password = :password";
            $params['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        
        $upd = $pdo->prepare($sql);
        $upd->execute($params);

        flash_set('success', 'Kullanıcı güncellendi.');
        header('Location: users.php');
        exit;
    }
}

$pageTitle = 'Kullanıcı Düzenle';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container mt-4 fade-in-up" style="max-width: 800px;">
    <div class="glass-card">
        <h4 class="mb-4"><i class="fas fa-user-edit" style="color: var(--primary-color)"></i> Kullanıcı Düzenle: <?= e($u['username']) ?></h4>

        <?php if ($m = flash_get('error')): ?>
            <div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>

        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">E-posta</label>
                <input name="email" type="email" class="form-control" value="<?= e($u['email']) ?>" required style="background: rgba(0,0,0,0.2);">
            </div>
            <div class="col-md-6">
                <label class="form-label">İsim Soyisim</label>
                <input name="full_name" class="form-control" value="<?= e($u['full_name']) ?>" style="background: rgba(0,0,0,0.2);">
            </div>
            <div class="col-md-6">
                <label class="form-label">Rol</label>
                <select name="role" class="form-select form-control" style="background: rgba(0,0,0,0.2); color: var(--text-main);">
                    <option value="employee" <?= $u['role'] === 'employee' ? 'selected' : '' ?>>Çalışan</option>
                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Yönetici</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Departman</label>
                <input name="department" class="form-control" value="<?= e($u['department']) ?>" style="background: rgba(0,0,0,0.2);">
            </div>
            <div class="col-12">
                <label class="form-label">Yeni Şifre (Boş bırakılırsa değişmez)</label>
                <input name="password" type="password" class="form-control" placeholder="••••••••" style="background: rgba(0,0,0,0.2);">
            </div>
            <div class="col-12 mt-4">
                <button class="btn btn-primary" style="width: auto; padding: 0.5rem 2rem;">Kaydet</button>
                <a href="users.php" class="btn" style="color: var(--text-muted); margin-left: 10px;">İptal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
