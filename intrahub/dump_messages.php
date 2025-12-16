<?php
require_once __DIR__ . '/includes/db.php';

echo "--- Last 5 Messages ---\n";
try {
    $stmt = $pdo->query("SELECT * FROM messages ORDER BY id DESC LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
