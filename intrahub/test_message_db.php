<?php
require_once __DIR__ . '/includes/db.php';

try {
    echo "Testing DB Connection...\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    echo "Users count: " . $stmt->fetchColumn() . "\n";

    echo "Attempting to insert test message...\n";
    
    // Check if sender/receiver exist (assuming user ID 1 exists, if not we might fail FK if we had one, but we don't have FK on messages table in the schema I see usually, let's check setup_database.sql)
    // In setup_database.sql, messages table does NOT have FOREIGN KEY constraints on sender_id/receiver_id, just KEY indexes. So insertion should work even with invalid IDs (unless sql_mode is strict, but usually OK).
    
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (:s, :r, :c)");
    $stmt->execute([
        's' => 999999, // Test ID
        'r' => 999999, // Test ID
        'c' => 'Test Message from Script ' . date('H:i:s')
    ]);
    
    $id = $pdo->lastInsertId();
    echo "Message inserted successfully. ID: $id\n";
    
    // Verify retrieval
    $stmt = $pdo->prepare("SELECT content FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    echo "Retrieved: " . $stmt->fetchColumn() . "\n";
    
    // Cleanup
    $pdo->exec("DELETE FROM messages WHERE id = $id");
    echo "Test message deleted.\n";

} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
