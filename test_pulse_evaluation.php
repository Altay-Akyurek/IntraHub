<?php
require_once __DIR__ . '/intrahub/includes/db.php';

// Test submitting a rating+comment to the pulse_responses table
// This matches existing API logic (action=submit) just with specific JSON content

try {
    echo "Testing Pulse Response Submission (Rating+Comment)...\n";
    
    // Get latest pulse ID
    $stmt = $pdo->query("SELECT id FROM pulses ORDER BY id DESC LIMIT 1");
    $pulse = $stmt->fetch();
    if(!$pulse) {
        die("No pulses found to test with.\n");
    }
    $pid = $pulse['id'];

    $answers = json_encode(['rating' => '5', 'comment' => 'CLI Test Comment']);
    
    $stmtResponse = $pdo->prepare("INSERT INTO pulse_responses (pulse_id, user_id, answers, reporter_hash) VALUES (:pid, :uid, :answers, :rh)");
    $stmtResponse->execute([
        'pid' => $pid,
        'uid' => 1, 
        'answers' => $answers,
        'rh' => 'cli_hash_' . time()
    ]);
    
    echo "Pulse Response Submitted for Pulse ID $pid.\n";
    echo "Verification Successful!\n";

} catch (PDOException $e) {
    echo "Test Failed: " . $e->getMessage() . "\n";
}
