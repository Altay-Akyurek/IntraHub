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
        } else {
            $validReset = $row;
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validReset) {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    if ($password === '' || $password !== $password_confirm) {
        $errors[] = 'Şifre boş olamaz ve şifreler eşleşmelidir.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $pdo->prepare("UPDATE users SET password =:p WHERE id =:id");
        $upd->execute(['p' => $hash, 'id' => $validReset['user_id']]);

        //kullanıcının token sil.
        $del = $pdo->prepare("DELETE FROM password_resets WHERE id =:id");
        $del->execute(['id' => $validReset['id']]);

        $success = "Parolanız başarıyla değiştirildi .Yeni Şifrenizle Giriş Yapabilrisiniz.";
    }
}
;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

</body>

</html>