<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

$flash = flash_get('info') ?: flash_get('success') ?: flash_get('error');

$stmt = $pdo->query("SELECT id, title, description, active, send_at, created_at FROM pulses ORDER BY created_at DESC");
$pulses = $stmt->fetchAll();
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pulse Yönetimi - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .q-row {
            margin-bottom: 8px
        }
    </style>
</head>

<body class="p-4">
    <div class="container">
        <h3>Pulse (Kısa Anket) Yönetimi</h3>
        <?php if ($flash): ?>
            <div class="alert alert-info"><?= e($flash) ?></div><?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h5>Yeni Pulse Oluştur</h5>
                <form id="pulseCreateForm" method="post" action="/pulses/api.php">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label class="form-label">Başlık</label>
                        <input name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gönderim zamanı (opsiyonel)</label>
                        <input type="datetime-local" name="send_at" class="form-control">
                        <div class="form-text">Boş bırakılırsa hemen aktif olur.</div>
                    </div>

                    <div id="questionsBox" class="mb-3">
                        <label class="form-label">Sorular</label>
                        <div id="questionsList"></div>
                        <button type="button" id="addQuestionBtn" class="btn btn-sm btn-outline-primary mt-2">Soru
                            Ekle</button>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="active" id="activeCheckbox" class="form-check-input" checked>
                        <label for="activeCheckbox" class="form-check-label">Aktif</label>
                    </div>

                    <button class="btn btn-primary">Oluştur</button>
                </form>
            </div>
        </div>

        <h5>Mevcut Pulses</h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Başlık</th>
                    <th>Gönderim</th>
                    <th>Aktif</th>
                    <th>Oluşturma</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pulses as $p): ?>
                    <tr>
                        <td><?= e($p['id']) ?></td>
                        <td><?= e($p['title']) ?><br><small class="text-muted"><?= e($p['description']) ?></small></td>
                        <td><?= e($p['send_at'] ?: 'hemen') ?></td>
                        <td><?= $p['active'] ? 'Evet' : 'Hayır' ?></td>
                        <td><?= e($p['created_at']) ?></td>
                        <td>
                            <a class="btn btn-sm btn-secondary"
                                href="/admin/pulse_responses.php?id=<?= e($p['id']) ?>">Cevaplar</a>
                            <form method="post" action="/pulses/api.php" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= e($p['id']) ?>">
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Silmek istediğine emin misin?')">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/pulses.js"></script>
</body>

</html>