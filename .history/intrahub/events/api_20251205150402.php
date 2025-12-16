<?php
//event/api.php -FullCaledar backend (list,creat,update,delete,attend)
//JSON in/out. POST with action for create/update/delete/attend
//GET returns list of event list events for calendar.

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT id, title, description, location, start_at as start, end_at as end FROM events");
    $rows = $stmt->fetchAll();
    //map to FullCalendar expected fields

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
    }, $row);
    echo json_encode($events);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $payload['action'] ?? '';

$user = current_user();
$isAdmin = $user['role'] === 'admin';

//create
if ($action === 'create') {
    if (!$isAdmin) {
        http_response_code(403);
        echo json_encode(['error' => 'Yetki Yok']);
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


    $stmt = $pdo->prepare("INSERT INTO events (title, description, location, start_at, end_at, created_by) VALUES (:title,:desc,:loc,:start,:end,:cb)");
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
//update
if ($action === 'update') {
    if (!$isAdmin) {
        http_response_code(403);
        echo json_encode(['error' => 'Yetki Yok']);
        exit;
    }
    $id = intval($payload['id' ?? 0]);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Eksik İD']);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE events SET title = :title, description = :desc, location = :loc, start_at = :start, end_at = :end WHERE id = :id");

}



?>