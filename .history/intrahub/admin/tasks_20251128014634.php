<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

//Basit Görev oluşturma sistemi,
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $assigned_to = intval($_POST['assigned_to'] ?? 0);
    $priority = in_array($_POST['priority'] ?? '', ['low', 'medium', 'high']) ? $_POST['priority'] : 'medium';
    $due_date = $_POST['due_date'] ?: null;

    if ($title === '') {
        flash_set('error', 'Başlık Zorunludur');
    } else {
        $sql = "INSERT INTO tasks (title, description, created_by, assigned_to, priority, due_date) VALUES (:title,:description,:created_by,:assigned_to,:priority,:due_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'created_by' => $created_by,
            'assigned_to' => $assigned_to,
            'priority' => $priority,
            'due_date' => $due_date
        ]);
        flash_set('success', 'Görev Oluşturuldu');
        header('Location :/admin/tasks.php');
        exit;
    }
}
//Görev Listesi
$stmt = $pdo->query("SELECT t.*, u.username as creator, ausername as assignee FROM tasks t LEFT JOIN users u ON t.created_by = u.id LEFT JOIN users a ON t.assigned_to = a.id ORDER BY t.created_at DESC");
$task = $stmt->fetchAll();

//Kullanıcı Listesi(Atama için)
$users = $pdo->query("SELECT id, username, full_name FROM users ORDER BY username")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Görev Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="p-4">
    <div class="container">
        <h3>Görevler</h3>
        <?php if ($m = flash_get('error')): ?>
            <div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
        <?php if ($m = flash_get('success')): ?>
            <div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5>Yeni Görev Oluştur</h5>
                <form method="post" class="row g-3">
                    <input type="hidden" name="action" value="create">
                    <div class="col-md-6">
                        <input name="title" class="form-control" placeholder="Başlık" required>
                    </div>
                    <div class="col-md-6">
                        <select name="assigned_to" class="form-select">
                            <option value="">Atama yok</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= e($u['id']) ?>"><?= e($u['username']) ?> - <?= e($u['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>