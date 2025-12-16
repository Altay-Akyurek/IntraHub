<?php
// events/api.php - FullCalendar backend (list, create, update, delete, attend, attendees list)

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];

$user = current_user();
$isAdmin = ($user['role'] ?? 'user') === 'admin';

/* -----------------------------
   GET: Event list & attendees list
------------------------------ */
if ($method === 'GET') {

    // Eğer attendees param geldiyse → katılımcıları döndür
    if (isset($_GET['attendees']) && isset($_GET['id'])) {
        $eventId = intval($_GET['id']);
        if (!$eventId) {
            http_response_code(400);
            echo json_encode(['error' => 'Eksik veya geçersiz ID']);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT ea.status, ea.responded_at, 
                   u.id AS user_id, u.username, u.full_name, u.department
            FROM event_attendance ea 
            JOIN users u ON ea.user_id = u.id 
            WHERE ea.event_id = :eid 
            ORDER BY ea.responded_at DESC
        ");
        $stmt->execute(['eid' => $eventId]);
        echo json_encode(['ok' => true, 'attendees' => $stmt->fetchAll()]);
        exit;
    }

    // Normal Event listesi
    $stmt = $pdo->query("SELECT id, title, description, location, start_at as start, end_at as end FROM events");
    $rows = $stmt->fetchAll();

    $events = array_map(function ($r) {
        return [
            'id' => $r['id'],
            'title' => $r['title'],
            'start' => $r['start'],
            'end' => $r['end'],
            'extendedProps' => [
                'description' => $r['description'],
                'location' => $r['location']
            ]
        ];
    }, $rows);

    echo json_encode($events);
    exit;
}

/* -----------------------------
   POST
------------------------------ */
$payload = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $payload['action'] ?? '';

/* -----------------------------
   CREATE (Admin)
------------------------------ */
if ($action === 'create') {
    if (!$isAdmin) {
        http_response_code(403);
        echo json_encode(['error' => 'Yetki yok']);
        exit;
    }

    $title = trim($payload['title'] ?? '');
    $description = trim($payload['description'] ?? '');
    $location = trim($payload['location'] ?? '');
    $start = $payload['start'] ?? null;
    $end = $payload['end'] ?? null;

    if ($title === '' || !$start) {
        http_response_code(400);
        echo json_encode(['error' => 'Başlık ve başlangıç zamanı zorunlu']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO events (title, description, location, start_at, end_at, created_by) 
        VALUES (:title, :desc, :loc, :start, :end, :cb)
    ");
    $stmt->execute([
        'title' => $title,
        'desc' => $description,
        'loc' => $location,
        'start' => $start,
        'end' => $end ?: null,
        'cb' => $user['id']
    ]);

    echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

/* -----------------------------
   UPDATE (Admin)
------------------------------ */
if ($action === 'update') {
    if (!$isAdmin) {
        http_response_code(403);
        echo json_encode(['error' => 'Yetki yok']);
        exit;
    }

    $id = intval($payload['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Eksik ID']);
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE events 
        SET title = :title, description = :desc, location = :loc, start_at = :start, end_at = :end 
        WHERE id = :id
    ");
    $stmt->execute([
        'title' => trim($payload['title'] ?? ''),
        'desc' => trim($payload['description'] ?? ''),
        'loc' => trim($payload['location'] ?? ''),
        'start' => $payload['start'] ?? null,
        'end' => $payload['end'] ?? null,
        'id' => $id
    ]);

    echo json_encode(['ok' => true]);
    exit;
}

/* -----------------------------
   DELETE (Admin)
------------------------------ */
if ($action === 'delete') {
    if (!$isAdmin) {
        http_response_code(403);
        echo json_encode(['error' => 'Yetki yok']);
        exit;
    }

    $id = intval($payload['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Eksik ID']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
    $stmt->execute(['id' => $id]);

    echo json_encode(['ok' => true]);
    exit;
}

/* -----------------------------
   ATTEND Toggle
------------------------------ */
if ($action === 'attend') {
    $eventId = intval($payload['id'] ?? 0);
    if (!$eventId) {
        http_response_code(400);
        echo json_encode(['error' => 'Eksik ID']);
        exit;
    }

    $me = $user['id'];

    // var mı?
    $stmt = $pdo->prepare("SELECT id, status FROM event_attendance WHERE event_id = :eid AND user_id = :uid LIMIT 1");
    $stmt->execute(['eid' => $eventId, 'uid' => $me]);
    $row = $stmt->fetch();

    if ($row) {
        $newStatus = ($row['status'] === 'attending') ? 'not_attending' : 'attending';
        $upd = $pdo->prepare("UPDATE event_attendance SET status = :st, responded_at = NOW() WHERE id = :id");
        $upd->execute(['st' => $newStatus, 'id' => $row['id']]);
        echo json_encode(['ok' => true, 'status' => $newStatus]);
    } else {
        $ins = $pdo->prepare("
            INSERT INTO event_attendance (event_id, user_id, status, responded_at) 
            VALUES (:eid, :uid, 'attending', NOW())
        ");
        $ins->execute(['eid' => $eventId, 'uid' => $me]);
        echo json_encode(['ok' => true, 'status' => 'attending']);
    }
    exit;
}

/* -----------------------------
   Varsayılan
------------------------------ */
http_response_code(400);
echo json_encode(['error' => 'Bilinmeyen action']);
exit;

