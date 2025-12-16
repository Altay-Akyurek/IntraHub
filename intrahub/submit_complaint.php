<?php
ini_set('display_errors', 0);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/json; charset=utf-8');
require_login();

try {
    $payload = json_decode(file_get_contents('php://input'), true);
    $action = $payload['action'] ?? 'submit';
    $user = current_user();

if ($action === 'submit') {
    $content = trim($payload['content'] ?? '');
    $anonymous = !empty($payload['anonymous']);

    if ($content === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Mesaj boş olamaz']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO complaints (user_id, content, is_anonymous) VALUES (:uid, :content, :anon)");
    $stmt->execute([
        'uid' => $anonymous ? null : $user['id'],
        'content' => $content,
        'anon' => $anonymous ? 1 : 0
    ]);

    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'delete') {
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Yetki yok']);
        exit;
    }
    $id = intval($payload['id'] ?? 0);
    $pdo->prepare("DELETE FROM complaints WHERE id = ?")->execute([$id]);
    echo json_encode(['ok' => true]);
    exit;
}
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server Hatası: ' . $e->getMessage()]);
}
?>
