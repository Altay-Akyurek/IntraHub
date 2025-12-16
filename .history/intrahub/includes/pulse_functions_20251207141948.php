<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';
/* generate_reporter_hash - user_id -> one-way hash using secret salt if available  */
function generate_reporter_hash($userId)
{
    $salt = getenv('PULSE_SALT') ?: 'intrahub_default_pulse_salt';
    return hash('sha256', $userId . '|' . $salt);
}

/* get_pulses- optionally filter active only */

function get_pulses($onlyActive = true)
{
    global $pdo;
    if ($onlyActive) {
        $stmt = $pdo->prepare("SELECT * FROM pulses WHERE active = 1 AND (send_at IS NULL OR send_at <= NOW()) ORDER BY created_at DESC");
        $stmt->execute();
    } else {
        $stmt = $pdo->query("SELECT * FROM pulses ORDER BY created_at DESC");
    }
    return $stmt->fetchAll();
}

/* get_responses_for_pulse */

function get_responses_for_pulse($pulseId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT pr.*, u.username, u.full_name FROM pulse_responses pr LEFT JOIN users u ON pr.user_id = u.id WHERE pr.pulse_id = :pid ORDER BY pr.created_at DESC");
}
?>