<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

/* Güvenli output için e() fonksiyonu */
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard-IntraHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-light bg-light">
        <div class="container">
            <span class="navbar-brand">IntraHub</span>
            <div>
                Hoşgeldin, <?= e($user['full-name'] ?? $user['username']) ?>
                <a href="../intrahub/admin/users.php">Yönetim</a> |<!-- Örnek kullanım -->
                <a href="../intrahub//logout.php">Çıkış</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3>Panel</h3>
        <p>Buraya görev özetleri, bildirimleri ve etkinlikler gelecek</p>
    </div>
</body>

</html>