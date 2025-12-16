<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = intval($_GET['id'] ?? 0);

if ($id && $id !== $_SESSION['user']['id']) { // Check self delete
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    flash_set('success', 'Kullanıcı silindi.');
} else {
    flash_set('error', 'Geçersiz işlem veya kendi kendinizi silemezsiniz.');
}

header('Location: users.php');
exit;
