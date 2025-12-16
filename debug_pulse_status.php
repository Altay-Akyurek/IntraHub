<?php
require_once __DIR__ . '/intrahub/includes/db.php';

date_default_timezone_set('Europe/Istanbul'); // Ensure we are checking with the likely target timezone

echo "Current Server Time (PHP): " . date('Y-m-d H:i:s') . "\n";

try {
    $stmt = $pdo->query("SELECT NOW() as db_time");
    $dbTime = $stmt->fetchColumn();
    echo "Current DB Time: " . $dbTime . "\n\n";

    echo "Listing Pulses:\n";
    $stmt = $pdo->query("SELECT id, title, active, send_at, created_at FROM pulses");
    $pulses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pulses as $p) {
        echo "ID: " . $p['id'] . "\n";
        echo "Title: " . $p['title'] . "\n";
        echo "Active: " . $p['active'] . "\n";
        echo "Send At: " . ($p['send_at'] ?: 'NULL') . "\n";
        
        $isAvailable = true;
        if (!$p['active']) {
            echo "-> Status: INACTIVE (active=0)\n";
            $isAvailable = false;
        }
        
        if ($p['send_at']) {
            $sendAt = new DateTime($p['send_at']);
            $now = new DateTime();
            if ($sendAt > $now) {
                echo "-> Status: PENDING (Future send_at: " . $sendAt->format('Y-m-d H:i:s') . " > Now: " . $now->format('Y-m-d H:i:s') . ")\n";
                $isAvailable = false;
            }
        }
        
        if ($isAvailable) {
            echo "-> Status: OK (Available)\n";
        }
        echo "-------------------\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
