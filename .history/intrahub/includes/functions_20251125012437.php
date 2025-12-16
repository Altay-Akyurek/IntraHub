<?php
function e($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function flash_set($key, $msg)
{
    if (!isset($_SESSION))
        session_start();
    $_SESSION['flash'][$key] = $msg;
}

function flash_get($key)
{
    if (!isset($_SESSION))
        session_start();
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}
?>