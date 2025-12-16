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
?>