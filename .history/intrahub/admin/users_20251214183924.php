<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php'; // <- e() ve flash helpers için gerekli

// Basit kullanıcı listesi
// ... PHP Code part ...
// Basit kullanıcı listesi
$stmt = $pdo->query("SELECT id, username, full_name, email, role, department FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();

$pageTitle = 'Kullanıcı Yönetimi';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Standardize Styles -->
<style>
    /* Glass Table */
    .table {
        --bs-table-bg: transparent;
        --bs-table-color: var(--text-main);
        border-color: rgba(255, 255, 255, 0.05);
    }

    .table thead th {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table tbody tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .table td,
    .table th {
        white-space: nowrap;
        /* Prevent text overlap */
        vertical-align: middle;
    }

    /* Badges */
    .badge-glass {
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0.4em 0.8em;
        border-radius: 20px;
        font-weight: 500;
    }

    .badge-admin {
        background: rgba(236, 72, 153, 0.2);
        color: #f472b6;
        box-shadow: 0 0 10px rgba(236, 72, 153, 0.2);
    }

    .badge-user {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
    }

    /* User Avatar */
    .user-avatar-glass {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Action Buttons */
    .btn-glass-action {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-muted);
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-glass-action:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-1px);
        color: var(--text-main);
    }

    .btn-glass-delete:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.3);
    }
</style>

<div class="container mt-4 fade-in-up">
    <!-- Header Outside the Card (Matches Pulse Page) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><br>
            <a href="create_user.php" class="btn btn-primary"
                style="border-radius: 50px; padding-left: 1.5rem; padding-right: 1.5rem;">
                <i class="fas fa-plus"></i> Yeni Kullanıcı
            </a>
        </h3>


    </div>

    <?php if ($msg = flash_get('success')): ?>
        <div class="alert alert-success"
            style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #34d399; backdrop-filter: blur(5px);">
            <?= e($msg) ?>
        </div>
    <?php endif; ?>
    <?php if ($msg = flash_get('error')): ?>
        <div class="alert alert-danger"
            style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; backdrop-filter: blur(5px);">
            <?= e($msg) ?>
        </div>
    <?php endif; ?>
    <br>
    <div class="glass-card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <h3><i class="fas fa-users"
                                style="color: var(--primary-color); filter: drop-shadow(0 0 5px rgba(99, 102, 241, 0.4));"></i>
                            Kullanıcılar
                        </h3>
                        <th class="ps-4">Kullanıcı</th>
                        <th>İsim</th>
                        <th>E-posta</th>
                        <th>Rol</th>
                        <th>Departman</th>
                        <th class="text-end pe-4">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar-glass">
                                        <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                    </div>
                                    <span class="fw-medium" style="color: var(--text-main);"><?= e($u['username']) ?></span>
                                </div>
                            </td>
                            <td style="color: var(--text-muted);"><?= e($u['full_name']) ?></td>
                            <td style="color: var(--text-muted); font-size: 0.9em;"><?= e($u['email']) ?></td>
                            <td>
                                <span
                                    class="badge badge-glass <?= $u['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                                    <?= $u['role'] === 'admin' ? 'Yönetici' : 'Kullanıcı' ?>
                                </span>
                            </td>
                            <td style="color: var(--text-muted);"><?= e($u['department'] ?: '-') ?></td>
                            <td class="text-end pe-4">
                                <a href="edit_user.php?id=<?= e($u['id']) ?>" class="btn btn-sm btn-glass-action me-1"
                                    title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete_user.php?id=<?= e($u['id']) ?>"
                                    class="btn btn-sm btn-glass-action btn-glass-delete" title="Sil"
                                    onclick="return confirm('Kullanıcıyı silmek istediğinize emin misiniz?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>