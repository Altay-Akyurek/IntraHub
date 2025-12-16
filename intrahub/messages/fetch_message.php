<?php
ini_set('display_errors', 0);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    require_login();
    
    $me = $_SESSION['user']['id'];
    $other = intval($_GET['user'] ?? 0);

    if ($other <= 0) {
        echo json_encode([]);
        exit;
    }

    $since_id = intval($_GET['since_id'] ?? 0);
    $before_id = intval($_GET['before_id'] ?? 0);
    $limit = intval($_GET['limit'] ?? 0); 
    
    // Default limit for history/initial load if not specified
    if ($limit <= 0) $limit = 6; 
    
    // Use positional parameters to avoid HY093 errors with some PDO drivers
    $sql = "
        SELECT m.*, s.username AS sender, r.username AS receiver 
        FROM messages m 
        LEFT JOIN users s ON m.sender_id = s.id 
        LEFT JOIN users r ON m.receiver_id = r.id 
        WHERE ((m.sender_id = ? AND m.receiver_id = ?) 
           OR (m.sender_id = ? AND m.receiver_id = ?))
    ";
    
    $params = [$me, $other, $other, $me];
    
    if ($since_id > 0) {
        // Polling for new messages
        $sql .= " AND m.id > ? ORDER BY m.created_at ASC";
        $params[] = $since_id;
        // No limit needed for polling, usually
    } elseif ($before_id > 0) {
        // Loading older history
        $sql .= " AND m.id < ? ORDER BY m.created_at DESC LIMIT $limit";
        $params[] = $before_id;
    } else {
        // Initial load (latest messages)
        $sql .= " ORDER BY m.created_at DESC LIMIT $limit";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If we fetched using DESC (History or Initial), we need to reverse to show chronologically
    if ($since_id == 0) {
        $rows = array_reverse($rows);
    }

    echo json_encode($rows);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Fetch Error: ' . $e->getMessage()]);
}
?>