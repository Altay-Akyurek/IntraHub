<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/pulse_functions.php';

$user = current_user();

// Listele aktif pulse'lar
$stmt = $pdo->prepare("SELECT id, title, description, questions, send_at FROM pulses WHERE active = 1 AND (send_at IS NULL OR send_at <= NOW()) ORDER BY created_at DESC");
$stmt->execute();
$pulses = $stmt->fetchAll();

// Basit single-response endpoint is handled in pulses/api.php via POST 'submit'
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Aktif Pulses - IntraHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <div class="container">
        <h3>Aktif Anketler</h3>

        <?php if (empty($pulses)): ?>
            <div class="alert alert-info">Şu an aktif anket yok.</div>
        <?php endif; ?>

        <?php foreach ($pulses as $p): ?>
            <?php $questions = json_decode($p['questions'], true) ?: []; ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5><?= e($p['title']) ?></h5>
                    <p class="text-muted"><?= e($p['description']) ?></p>

                    <form class="pulseForm" data-pulse-id="<?= e($p['id']) ?>">
                        <?php foreach ($questions as $qi => $q): ?>
                            <div class="mb-3">
                                <label class="form-label"><?= e($q['text']) ?></label>
                                <?php if (($q['type'] ?? '') === 'scale'): ?>
                                    <select name="q<?= $qi ?>" class="form-select" required>
                                        <option value="">Seçiniz</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                <?php else: ?>
                                    <textarea name="q<?= $qi ?>" class="form-control" required></textarea>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <button class="btn btn-primary submitBtn">Gönder</button>
                    </form>

                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <script>
        document.querySelectorAll('.pulseForm').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var pid = form.getAttribute('data-pulse-id');
                var formData = new FormData(form);
                var answers = {};
                for (var pair of formData.entries()) {
                    answers[pair[0].replace(/^q/, '')] = pair[1];
                }
                fetch('/pulses/api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'submit', pulse_id: pid, answers: answers })
                }).then(r => r.json()).then(function (resp) {
                    if (resp.ok) {
                        alert('Cevabınız kaydedildi. Teşekkürler.');
                        form.querySelectorAll('input,textarea,select,button').forEach(function (el) { el.disabled = true; });
                    } else {
                        alert('Hata: ' + (resp.error || 'Bilinmeyen'));
                    }
                }).catch(function (err) { alert('İstek başarısız: ' + err); });
            });
        });
    </script>
</body>

</html>