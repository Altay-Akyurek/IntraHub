<?php
ini_set('display_errors', 0);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if (!is_logged_in()) {
        throw new Exception("Oturum açmanız gerekiyor.", 401);
    }

    $input = file_get_contents('php://input');
    $payload = json_decode($input, true);
    
    if (!$payload) {
        throw new Exception("Geçersiz JSON verisi.");
    }

    $to = intval($payload['to'] ?? 0);
    $content = trim($payload['content'] ?? '');

    if ($to <= 0) throw new Exception("Alıcı seçilmedi.");
    if ($content === '') throw new Exception("Mesaj içeriği boş olamaz.");

    $senderId = $_SESSION['user']['id'];

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$senderId, $to, $content]);

    echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);

} catch (Exception $e) {
    $code = $e->getCode() === 401 ? 401 : 500;
    http_response_code($code);
    echo json_encode(['error' => $e->getMessage()]);
}
?>