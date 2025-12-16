<?php
require_once __DIR__ . '/includes/db.php';

function describeTable($table) {
    global $pdo;
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        echo "Table: $table\n";
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
            echo " - " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
        echo "\n";
    } catch (PDOException $e) {
        echo "Table $table error: " . $e->getMessage() . "\n";
    }
}

describeTable('pulses');
describeTable('messages');
describeTable('complaints');
