<?php
if (!isset($_SESSION))
    session_start();

/* -----------------------------
   VERİTABANI BAĞLANTISI
------------------------------ */
// DB bağlantısı db.php üzerinden yapılmalıdır.
// Bu dosya dahili fonksiyonları barındırır.

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
   CSRF TOKEN
------------------------------ */
function csrf_token()
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/* -----------------------------
   CSRF DOĞRULAMA
------------------------------ */
function csrf_verify($token)
{
    return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
}
