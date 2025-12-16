<?php
if (!isset($_SESSION))
    session_start();

/* -----------------------------
   VERİTABANI BAĞLANTISI
------------------------------ */
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=company_chat;charset=utf8",
        "root",
        ""
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

/* -----------------------------
   GÜVENLİ YAZI FONKSİYONU
------------------------------ */
function e($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/* -----------------------------
   FLASH MESAJ SET
------------------------------ */
function flash_set($key, $msg)
{
    $_SESSION['flash'][$key] = $msg;
}

/* -----------------------------
   FLASH MESAJ GET
------------------------------ */
function flash_get($key)
{
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

/* -----------------------------
   LOGIN FONKSİYONU
------------------------------ */
// Kullanıcı adı veya e-posta ile giriş destekliyor
function login($username, $password)
{
    global $pdo;

    // Kullanıcıyı kullanıcı adı veya e-posta ile bul
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return false; // Kullanıcı yok
    }

    // Şifre doğrulama
    if (password_verify($password, $user['password'])) {
        // Giriş başarılı
        $_SESSION['user'] = [
            "id" => $user['id'],
            "username" => $user['username'],
            "role" => $user['role']
        ];
        return true;
    }

    return false; // Şifre hatalı
}

/* -----------------------------
   LOGIN ZORUNLULUĞU (İsteğe Bağlı)
------------------------------ */
function require_login()
{
    if (!isset($_SESSION['user'])) {
        header("Location: index.php");
        exit;
    }
}
