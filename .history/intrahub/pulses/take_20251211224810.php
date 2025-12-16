<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/pulse_functions.php';

$user = current_user();

//Listele aktif pulse'lar
$stmt = $pdo->prepare("SELECT id, title, description, questions, send_at FROM pulses WHERE active = 1 AND (send_at IS NULL OR send_at <= NOW()) ORDER BY created_at DESC");
$stmt->execute();
$pulses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

</body>

</html>