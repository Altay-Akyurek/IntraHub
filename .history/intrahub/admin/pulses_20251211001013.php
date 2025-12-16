<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

$flash = flash_get('info') ?: flash_get('success') ?: flash_get('error');

$stmt = $pdo->query("SELECT id, title, description, active, send_at, created_at FROM pulses ORDER BY created_at DESC");
$pulse = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .q-row {
            margin-bottom: 8px
        }
    </style>

</head>

<body>

</body>

</html>