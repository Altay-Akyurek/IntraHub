<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

$flash = flash_get('info') ?: flash_get('success') ?: flash_get('error');

$stmt = $pdo->query("SELECT id, title, description, active, send_at, created_at FROM pulses ORDER BY created_at DESC");
$pulse = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pulse Yönetim -ADMİN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .q-row {
            margin-bottom: 8px
        }
    </style>

</head>

<body class="p-4">
    <div class="container">
        <h3>PULSE (Kısa Anket) Yönetimi</h3>
        <?php if ($flash): ?>
            <div class="alert alert-info"><?= e($flash) ?></div><?php endif; ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5>Yeni Pulse Oluştur</h5>
                <form method="post" id="pulseCreateForm" action="/pulse/api.php">
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
                        <label class="form-label">Gönderim Zamanı(opsiyonel)</label>
                        <input type="datetime" name="send_at" class="form-control">
                        <div class="form-text">Boş Bırakılırsa Hemen Aktif Olur.</div>
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
                </form>
            </div>
        </div>
    </div>

</body>

</html>