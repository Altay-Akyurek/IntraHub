<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    flash_set('error', 'Eksik pulse id');
    header('Location: /admin/pulses.php');
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM pulses WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$pulse = $stmt->fetch();
if (!$pulse) {
    flash_set('error', 'Pulse bulunamadı');
    header('Location: /admin/pulses.php');
    exit;
}

$stmt2 = $pdo->prepare("SELECT pr.*, u.username, u.full_name FROM pulse_responses pr LEFT JOIN users u ON pr.user_id = u.id WHERE pr.pulse_id = :pid ORDER BY pr.created_at DESC");
$stmt2->execute(['pid' => $id]);
$responses = $stmt2->fetchAll();

// parse questions and compute basic stats for 'scale' type questions
$questions = json_decode($pulse['questions'], true) ?: [];
// compute averages for scale questions
$stats = [];
foreach ($questions as $qi => $q) {
    if (($q['type'] ?? '') === 'scale') {
        $sum = 0;
        $count = 0;
        foreach ($responses as $r) {
            $answers = json_decode($r['answers'], true) ?: [];
            if (isset($answers[$qi]) && is_numeric($answers[$qi])) {
                $sum += (float) $answers[$qi];
                $count++;
            }
        }
        $stats[$qi] = $count ? round($sum / $count, 2) : null;
    }
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pulse Cevapları - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <div class="container">
        <h4>Pulse: <?= e($pulse['title']) ?></h4>
        <p><?= e($pulse['description']) ?></p>

        <h5>Genel İstatistik</h5>
        <?php if (empty($stats)): ?>
            <div class="alert alert-info">Henüz ölçek tipi soru yok veya veri yok.</div>
        <?php else: ?>
            <ul>
                <?php foreach ($stats as $qi => $avg): ?>
                    <li><?= e($questions[$qi]['text']) ?> — Ortalama: <?= $avg === null ? '—' : e($avg) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h5 class="mt-4">Tüm Cevaplar (<?= count($responses) ?>)</h5>
        <?php if (empty($responses)): ?>
            <div class="alert alert-secondary">Henüz cevap yok.</div>
        <?php else: ?>
            <?php foreach ($responses as $r): ?>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div><strong><?= e($r['full_name'] ?: $r['username'] ?: 'Anonim') ?></strong></div>
                            <div class="text-muted small"><?= e($r['created_at']) ?></div>
                        </div>
                        <div class="mt-2">
                            <?php 
                            $answers = json_decode($r['answers'], true) ?: []; 
                            // Check for new format (rating/comment) or fallback to raw dump if structure is unknown
                            $rating = $answers['rating'] ?? null;
                            $comment = $answers['comment'] ?? null;
                            ?>
                            
                            <?php if ($rating || $comment): ?>
                                <?php if ($rating): ?>
                                    <div class="mb-2">
                                        <strong>Puan:</strong> 
                                        <?php for($i=0; $i<$rating; $i++) echo '<span style="color:#f59e0b">★</span>'; ?>
                                        <span class="text-muted">(<?= $rating ?>/5)</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($comment): ?>
                                    <div>
                                        <strong>Yorum:</strong><br>
                                        <?= nl2br(e($comment)) ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- Legacy Support or Fallback -->
                                <?php foreach ($answers as $k => $v): ?>
                                    <div>
                                        <strong><?= is_numeric($k) ? "Soru ".($k+1) : e($k) ?>:</strong>
                                        <?= e(is_array($v) ? json_encode($v) : $v) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="/admin/pulses.php" class="btn btn-secondary mt-3">Geri</a>
    </div>
</body>

</html>