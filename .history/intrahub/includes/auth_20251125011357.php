<?php
session_start();
require_once __DIR__ . 'db.php';

function login($usernameOrEmail, $password)
{
    global $pdo;
    $sql = "SELECT * FROM users WHERE username = :u OR email= :u LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['u' => $usernameOrEmail]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'full_name' => $user['full_name']
        ];
        return true;
    }
    return false;

}
function is_logged_in()
{
    return isset($_SESSION['user']);
}

function current_user()
{
    return $_SESSION['user'] ?? null;
}

function require_login()
{
    if (!is_logged_in()) {
        header('Loocation:/index.php');
        exit;
    }
}

function require_role($role)
{
    if (!is_logged_in() || $_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        echo "Erişim reddedildi.";
        exit;
    }
}
?>