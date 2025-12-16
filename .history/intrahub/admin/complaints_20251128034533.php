<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

//Listele
$stmt = $pdo->query("SELECT id,title,category,created_at FROM complaints ORDER BY created_at DESC");
$complaints = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anonim Şikayetler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="p-4">
    <div class="container">
        <h3>Anonim Şikayetler</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Başlık</th>
                    <th>Kategori</th>
                    <th>Geliş</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $c): ?>
                    <tr>
                        <td><?= e($c['id']) ?></td>
                        <td><?= e($c['title'] ?: '(başlıksız)') ?></td>
                        <td><?= e($c['category']) ?></td>
                        <td><?= e($c['created_at']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#cModal<?= e($c['id']) ?>">Görüntüle</button>
                        </td>
                    </tr>

                    <!-- Modal detay -->
                    <div class="modal fade" id="cModal<?= e($c['id']) ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Şikayet #<?= e($c['id']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <?php
                                    $stmt2 = $pdo->prepare("SELECT * FROM complaints WHERE id = :id LIMIT 1");
                                    $stmt2->execute(['id' => $c['id']]);
                                    $detail = $stmt2->fetch();
                                    ?>
                                    <p><strong>Kategori:</strong> <?= e($detail['category']) ?></p>
                                    <p><strong>Mesaj:</strong><br><?= nl2br(e($detail['message'])) ?></p>
                                    <p><small>Oluşturulma: <?= e($detail['created_at']) ?></small></p>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>