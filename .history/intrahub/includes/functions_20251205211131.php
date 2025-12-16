<?php
if (!isset($_SESSION))
    session_start();

/* -----------------------------
   VERİTABANI BAĞLANTISI
------------------------------ */
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=intrahub;charset=utf8",
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
function login($username, $password)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return false;
    }

    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'] ?? 'user'
        ];
        return true;
    }

    return false;
}

/* -----------------------------
   GİRİŞ ZORUNLU
------------------------------ */
function require_login()
{
    if (!isset($_SESSION['user'])) {
        header("Location: index.php");
        exit;
    }
}

function csrf_token()
{
    if (!isset($_SESSION))
        session_start();
    if (empty($_SESSION['_csrf_token'])) {
        return $_SESSION['_csrf_token'];
    }
}

function csrf_verify($token)
{
    if (!isset($_SESSION))
        session_start();
    return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
}