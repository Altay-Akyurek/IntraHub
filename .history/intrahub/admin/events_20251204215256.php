<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/functions.php';


//buradaki sayfa tüm girişli kullanıcı görebileçeği takvimdir
//oluşturma / düzenleme yanlızca adminler için gözükür
$idadmin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etkinlik Takvim -İntraHub</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <style>
        #calendar {
            max-width: 1100px;
            margin: 40px auto;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-light bg-light">
        <div class="container">
            <span class="navbar-brand">IntraHub</span>
            <div>
                Hoşgeldin, <?= e($_SESSION['user']['full_name'] ?? $_SESSION['user']['username']) ?> |
                <a href="../intrahub/dashboard.php">Panel</a> |
                <a href="../intrahub/logout.php">Çıkış</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="calendar"></div>
    </div>

    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="eventFrom" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Etkinlik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="evt_id">
                    <div class="mb-3">
                        <label class="form-label">Başlık</label>
                        <input class="form-control" name="title" id="evt_title" required>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>

</html>