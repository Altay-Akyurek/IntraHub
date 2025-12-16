<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/functions.php';

// Tüm girişli kullanıcılar takvimi görür.
// Oluşturma/düzenleme yalnızca adminler için görünür.
$isAdmin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Etkinlik Takvimi - IntraHub</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <style>
        #calendar {
            max-width: 1100px;
            margin: 40px auto;
        }

        .attendee-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-light bg-light">
        <div class="container">
            <span class="navbar-brand">IntraHub</span>
            <div>
                Hoşgeldin, <?= e($_SESSION['user']['full_name'] ?? $_SESSION['user']['username']) ?> |
                <a href="/dashboard.php">Panel</a> |
                <a href="/logout.php">Çıkış</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div id="calendar"></div>
    </div>

    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="eventForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Etkinlik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="evt_id">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-2"><strong>Başlık:</strong> <span id="evt_title_readonly"></span></div>
                            <div class="mb-2"><strong>Zaman:</strong> <span id="evt_time"></span></div>
                            <div class="mb-2"><strong>Konum:</strong> <span id="evt_location_readonly"></span></div>
                            <div class="mb-2"><strong>Açıklama:</strong>
                                <div id="evt_description_readonly" class="border p-2 bg-light"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <strong>Katılımcılar</strong>
                                <small id="attendee_count" class="text-muted">0</small>
                            </div>
                            <div id="evt_attendees"
                                style="max-height:300px; overflow:auto; border:1px solid #eee; border-radius:4px;">
                            </div>
                        </div>
                    </div>

                    <!-- Edit fields (only used when admin creates/edits) -->
                    <div id="evt_edit_fields" style="display:none" class="mt-3">
                        <div class="mb-3">
                            <label class="form-label">Başlık</label>
                            <input class="form-control" name="title" id="evt_title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Başlangıç</label>
                            <input type="datetime-local" class="form-control" name="start" id="evt_start">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bitiş</label>
                            <input type="datetime-local" class="form-control" name="end" id="evt_end">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konum</label>
                            <input class="form-control" name="location" id="evt_location">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Açıklama</label>
                            <textarea class="form-control" name="description" id="evt_description"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <?php if ($isAdmin): ?>
                        <button type="button" id="deleteBtn" class="btn btn-danger me-auto">Sil</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    <?php else: ?>
                        <button type="button" id="attendBtn" class="btn btn-success me-auto">Katıl</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.INTRAHUB = {
            isAdmin: <?= $isAdmin ? 'true' : 'false' ?>,
            currentUserId: <?= json_encode($_SESSION['user']['id']) ?>
        };
    </script>
    <script src="/assets/js/calendar.js"></script>
</body>

</html>