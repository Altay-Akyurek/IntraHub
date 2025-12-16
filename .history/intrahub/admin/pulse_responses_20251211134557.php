<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';


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

$questions = json_decode($pulse['questions'], true) ?: [];

$stats = [];
foreach ($questions as $qi => $q) {
    if (($q['type'] ?? '') === 'scale') {
        $sum = 0;
        $count = 0;

    }
}

?>