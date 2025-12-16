<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/mailer.php';
session_start();

//Eğer Kullanıcı zaten giriş yaptıysa dashboard a yönlendir.

if (isset($_SESSION['user'])) {
    header('Location: /dashboard.php');
    exit;
}

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $errors[] = 'Kullanıcı adı,e-posta ve şifre zorunludur.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Sifreler eşleşmiyor';
    }

    //E-posta ve kullanıcı adı benzersizmi,

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
        $stmt->execute(['u' => $username, 'e' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Kullanıcı adı veya e-posta zaten kullanılıyor.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, full_name, role) VALUES (:username,:email,:password,:full_name,'employee')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'full_name' => $full_name
        ]);
        $newId = $pdo->lastInsertId();
        $success = "Hesabınız oluşturuldu , giriş yapabilirsiniz.";

        //Hoşgeldiniz e-postası
        try {
            send_mail($email, 'IntraHub - Hoşgeldiniz', "Merhaba {$full_name},\n\nIntraHub hesabınız oluşturuldu. Kullanıcı adı: {$username}");
        } catch (Exception $e) {
            // sesssion flash yerine local mesaj
            $errors[] = "Hesap oluşturuldu ancak e-posta gönderilemedi.";
        }
    }



}
?>