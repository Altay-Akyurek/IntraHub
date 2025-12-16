<?php
ini_set('display_errors', 0);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/pulse_functions.php';
require_login();

header('Content-Type: application/json; charset=utf-8');

try {
    $input = file_get_contents('php://input');
    $payload = json_decode($input, true) ?? $_POST;
    $action = $payload['action'] ?? ($payload['a'] ?? $_GET['action'] ?? '');
    
    // Evaluate (Simple Rating)
    if ($action === 'evaluate') {
        $rating = intval($payload['rating'] ?? 0);
        $comment = trim($payload['comment'] ?? '');
        
        if ($rating < 1 || $rating > 5) {
            http_response_code(400);
            echo json_encode(['error' => 'Lütfen 1-5 arası bir puan verin']);
            exit;
        }
        
        $stmt = $pdo->prepare("INSERT INTO evaluations (user_id, rating, comment) VALUES (:uid, :rating, :comment)");
        $stmt->execute([
            'uid' => $user['id'],
            'rating' => $rating,
            'comment' => $comment
        ]);
        echo json_encode(['ok' => true]);
        exit;
    }
    
    // ... rest of logic
    if ($action === '') {
         // handle empty action if not covered below
    }

$user = current_user();

// Create pulse (admin)
if ($action === 'create') {
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Yetki yok']);
        exit;
    }
    $title = trim($payload['title'] ?? '');
    if ($title === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Başlık zorunlu']);
        exit;
    }
    $description = trim($payload['description'] ?? '');
    
    // Explicitly handle questions from JSON payload
    $questions = $payload['questions'] ?? [];
    
    // Robust parsing (in case it came as a string for some reason, though JSON input implies it's already an array in $payload)
    if (is_string($questions)) {
        $decoded = json_decode($questions, true);
        if (is_array($decoded)) {
            $questions = $decoded;
        }
    }
    
    if (empty($questions) || !is_array($questions)) {
        http_response_code(400);
        echo json_encode(['error' => 'En az bir soru ekleyin']);
        exit;
    }
    $send_at = $payload['send_at'] ?? null;
    $active = isset($payload['active']) && ($payload['active'] == '0' || $payload['active'] == 0) ? 0 : 1;

    $stmt = $pdo->prepare("INSERT INTO pulses (title, description, questions, send_at, active, created_by) VALUES (:title,:desc,:questions,:send_at,:active,:cb)");
    $stmt->execute([
        'title' => $title,
        'desc' => $description,
        'questions' => json_encode($questions, JSON_UNESCAPED_UNICODE),
        'send_at' => $send_at ?: null,
        'active' => $active,
        'cb' => $user['id']
    ]);
    echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

// Get Single Pulse (admin)
if ($action === 'get') {
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Yetki yok']);
        exit;
    }
    $id = intval($payload['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Eksik id']);
        exit;
    }
    $stmt = $pdo->prepare("SELECT * FROM pulses WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $pulse = $stmt->fetch();
    
    if($pulse) {
        // Decode questions for frontend
        $pulse['questions'] = json_decode($pulse['questions'], true);
        echo json_encode(['ok' => true, 'pulse' => $pulse]);
    } else {
        echo json_encode(['error' => 'Pulse bulunamadı']);
    }
    exit;
}

// Update Pulse (admin)
if ($action === 'update') {
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Yetki yok']);
        exit;
    }
    $id = intval($payload['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Eksik id']);
        exit;
    }
    
    $title = trim($payload['title'] ?? '');
    if ($title === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Başlık zorunlu']);
        exit;
    }
    $description = trim($payload['description'] ?? '');
    $questions = $payload['questions'] ?? [];
    
    if (is_string($questions)) {
        $decoded = json_decode($questions, true);
        if (is_array($decoded)) $questions = $decoded;
    }
    
    if (empty($questions) || !is_array($questions)) {
        http_response_code(400);
        echo json_encode(['error' => 'En az bir soru ekleyin']);
        exit;
    }
    
    $send_at = $payload['send_at'] ?? null;
    $active = isset($payload['active']) && ($payload['active'] == '0' || $payload['active'] == 0) ? 0 : 1;

    $stmt = $pdo->prepare("UPDATE pulses SET title=:title, description=:desc, questions=:questions, send_at=:send_at, active=:active WHERE id=:id");
    $stmt->execute([
        'title' => $title,
        'desc' => $description,
        'questions' => json_encode($questions, JSON_UNESCAPED_UNICODE),
        'send_at' => $send_at ?: null,
        'active' => $active,
        'id' => $id
    ]);
    echo json_encode(['ok' => true]);
    exit;
}

// Delete (admin)
if ($action === 'delete') {
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Yetki yok']);
        exit;
    }
    $id = intval($payload['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Eksik id']);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM pulses WHERE id = :id");
    $stmt->execute(['id' => $id]);
    echo json_encode(['ok' => true]);
    exit;
}

// Fetch pulses (public for logged users) - optional param all=1 to fetch all (admin)
if ($action === 'list' || ($_SERVER['REQUEST_METHOD'] === 'GET')) {
    if ($user['role'] === 'admin' && isset($_GET['all']) && $_GET['all'] == '1') {
        $rows = $pdo->query("SELECT * FROM pulses ORDER BY created_at DESC")->fetchAll();
    } else {
        $rows = get_pulses(true);
    }
    echo json_encode(['ok' => true, 'pulses' => $rows]);
    exit;
}

// Submit a response (employee or logged user)
if ($action === 'submit') {
    $pulseId = intval($payload['pulse_id'] ?? 0);
    if (!$pulseId) {
        http_response_code(400);
        echo json_encode(['error' => 'Eksik pulse_id']);
        exit;
    }
    $answers = $payload['answers'] ?? [];
    if (!is_array($answers)) {
        http_response_code(400);
        echo json_encode(['error' => 'Geçersiz answers']);
        exit;
    }

    // Check if pulse exists and is active
    $stmt = $pdo->prepare("SELECT id, active, send_at FROM pulses WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $pulseId]);
    $p = $stmt->fetch();
    if (!$p) {
        http_response_code(404);
        echo json_encode(['error' => 'Pulse bulunamadı']);
        exit;
    }
    if (!$p['active'] || ($p['send_at'] && new DateTime($p['send_at']) > new DateTime())) {
        http_response_code(403);
        echo json_encode(['error' => 'Bu pulse şu an kullanılamıyor']);
        exit;
    }

    // Build reporter_hash for semi-anonim
    $reporter_hash = isset($user['id']) ? generate_reporter_hash($user['id']) : null;
    $stmt = $pdo->prepare("INSERT INTO pulse_responses (pulse_id, user_id, answers, reporter_hash) VALUES (:pid,:uid,:answers,:rh)");
    $stmt->execute([
        'pid' => $pulseId,
        'uid' => $user['id'] ?? null,
        'answers' => json_encode($answers, JSON_UNESCAPED_UNICODE),
        'rh' => $reporter_hash
    ]);
    echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

// Fetch responses for a pulse (admin)
if ($action === 'responses') {
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Yetki yok']);
        exit;
    }
    $pid = intval($payload['pulse_id'] ?? $payload['id'] ?? 0);
    if (!$pid) {
        http_response_code(400);
        echo json_encode(['error' => 'Eksik pulse_id']);
        exit;
    }
    $rows = get_responses_for_pulse($pid);
    echo json_encode(['ok' => true, 'responses' => $rows]);
    exit;
}

    http_response_code(400);
    echo json_encode(['error' => 'Bilinmeyen action']);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server Hatası: ' . $e->getMessage()]);
}
?>