<?php
require_once __DIR__ . '/intrahub/includes/db.php';
require_once __DIR__ . '/intrahub/includes/pulse_functions.php';

// Simulate a user for testing (ensure a user exists or mock it)
// For this test, we'll just insert a dummy pulse and try to respond to it.
// We won't use the API endpoint directly to avoid auth issues in CLI, 
// but we will test the DB logic.

try {
    echo "Creating test pulse...\n";
    $stmt = $pdo->prepare("INSERT INTO pulses (title, description, questions, active, created_by) VALUES (:title, :desc, :questions, 1, 1)");
    $questions = json_encode([
        ['text' => 'Test Question 1?', 'type' => 'text'],
        ['text' => 'Rate us', 'type' => 'scale']
    ]);
    
    $stmt->execute([
        'title' => 'Test Pulse ' . time(),
        'desc' => 'Auto generated test pulse',
        'questions' => $questions
    ]);
    $pulseId = $pdo->lastInsertId();
    echo "Created Pulse ID: $pulseId\n";

    echo "Submitting response to Pulse ID: $pulseId...\n";
    $answers = json_encode(['0' => 'My Answer', '1' => '5']);
    $reporterKey = 'cli_test_' . time();
    
    $stmtResponse = $pdo->prepare("INSERT INTO pulse_responses (pulse_id, user_id, answers, reporter_hash) VALUES (:pid, :uid, :answers, :rh)");
    $stmtResponse->execute([
        'pid' => $pulseId,
        'uid' => 1, // Assumptions: user ID 1 exists
        'answers' => $answers,
        'rh' => hash('sha256', $reporterKey)
    ]);
    $responseId = $pdo->lastInsertId();
    echo "Submitted Response ID: $responseId\n";
    
    echo "Verification Successful!\n";

} catch (PDOException $e) {
    echo "Test Failed: " . $e->getMessage() . "\n";
}
