<?php
session_start();
require_once __DIR__ . 'db.php';

function login($usernameOrEmail, $password)
{
    global $pdo;
    $sql="SELECT * FROM users WHERE username = :u OR email= :u LIMIT 1";
    $stmt=$pdo->prepare($sql);
    $stmt->execute(['u'=>$usernameOrEmail]);
    $user=$stmt->fetch();
    if($user && password_verify($password,$user['password'])){
        session_regenerate_id(true);
        $_SESSION['user']=[
            'id'      =>    $user['id'],
            'username'=>    $user['username'],
            'role'    =>    $user['role'],
            'full_name'=>   $usernameOrEmail
        ]
    }

}
?>