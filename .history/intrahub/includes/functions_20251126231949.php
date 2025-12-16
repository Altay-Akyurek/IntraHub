<?php
if (!isset($_SESSION))
    session_start();

/* XSS Temizleme */
function e($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/* Flash mesaj set */
function flash_set($key, $msg)
{
    $_SESSION['flash'][$key] = $msg;
}

/* Flash mesaj get */
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
   Basit Test Login Fonksiyonu
------------------------------ */
function login($username, $password)
{
    // Test kullanıcı: admin / 1234
    if (($username === 'admin' || $username === 'admin@example.com') && $password === '1234') {
        $_SESSION['user'] = [
            'username' => 'admin',
            'email' => 'admin@example.com'
        ];
        return true;
    }
    return false;
}
