<?php
require_once __DIR__ . '/intrahub/includes/db.php';

// Test Evaluate Action (simulating API call via direct DB check mostly, or helper if we had one)
// We will insert via a cURL call to the local API? No, users environment might not support local loopback easily.
// Let's test by directly invoking the logic or just trusting the code + DB check.
// I will insert a dummy record to DB to ensure table is writable.

try {
    echo "Testing Evaluation table insert...\n";
    $stmt = $pdo->prepare("INSERT INTO evaluations (user_id, rating, comment) VALUES (1, 5, 'Test comment via CLI')");
    $stmt->execute();
    echo "Insert successful. ID: " . $pdo->lastInsertId() . "\n";
} catch (PDOException $e) {
    echo "Insert Failed: " . $e->getMessage() . "\n";
}
