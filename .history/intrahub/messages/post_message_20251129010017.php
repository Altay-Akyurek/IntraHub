<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$payload = json_decode(file_get_contents('php://input'), true);
$to = intval($payload['to'] ?? 0);
$content = trim($payload['content'] ?? '');

if ($to <= 0 || $content === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Eksik Parametre']);
    exit;
}
$stmt = $pdo->prepare("INSERT INTO message (sender_id,receiver_id,content) VALUES (:s,:r,:c)");
$stmt->execute([
    's' => $_SESSION['user']['id'],
    'r' => $to,
    'c' => $content,
]);

echo json_encode(['ok' => true]);
?>