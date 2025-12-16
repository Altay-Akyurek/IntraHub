<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$other = intval($_GET['user'] ?? 0);
if ($other <= 0) {
    echo json_encode([]);
    exit;
}

$me = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT m.*, s.username AS sender, r.username AS receiver FROM messages m LEFT JOIN users s ON m.sender_id = s.id LEFT JOIN users r ON m.receiver_id = r.id WHERE (m.sender_id = :me AND m.receiver_id = :other) OR (m.sender_id = :other AND m.receiver_id = :me) ORDER BY m.created_at ASC");
$stmt->execute(['me' => $me, 'other' => $other]);
$rows = $stmt->fetchAll();

echo json_encode($rows);
?>