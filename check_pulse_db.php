<?php
require_once __DIR__ . '/intrahub/includes/db.php';

echo "Checking pulses table:\n";
try {
    $stmt = $pdo->query("DESCRIBE pulses");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . " - " . $col['Type'] . "\n";
    }
} catch (PDOException $e) {
    echo "Error describing pulses: " . $e->getMessage() . "\n";
}

echo "\nChecking pulse_responses table:\n";
try {
    $stmt = $pdo->query("DESCRIBE pulse_responses");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . " - " . $col['Type'] . "\n";
    }
} catch (PDOException $e) {
    echo "Error describing pulse_responses: " . $e->getMessage() . "\n";
}
