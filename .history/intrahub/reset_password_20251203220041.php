<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
session_start();

$token = $_GET['token'] ?? ($_POST['token'] ?? null);
$errors = [];
$success = null;
$validReset = null;

if (!$token) {
    $errors[] = 'Geçersiz veya eksik token';
} else {
    //token dogrulama
    $stmt = $pdo->prepare("SELECT pr.*, u.email, u.full_name, u.id as user_id FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE pr.token = :t LIMIT 1");
    $stmt->execute(['t' => $token]);
    $row = $stmt->fetch();
    if (!$row) {
        $errors[] = 'Token Bulunamadı.';

    } else {
        $now = new DateTime();
        $expires = new DateTime($row['expires_at']);
        if ($now > $expires) {
            $errors[] = 'Tokenin Süresi dolmuştur';
        }
    }
}
?>