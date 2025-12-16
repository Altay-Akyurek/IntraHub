<?php
require_once __DIR__ . '/includes/db.php';

try {
    echo "Setting up pulses tables...\n";

    // Pulses table
    $pdo->exec("CREATE TABLE IF NOT EXISTS pulses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        questions TEXT, -- JSON array of questions
        active TINYINT(1) DEFAULT 1,
        send_at DATETIME NULL,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Table 'pulses' checked/created.\n";

    // Pulse responses table
    $pdo->exec("CREATE TABLE IF NOT EXISTS pulse_responses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pulse_id INT NOT NULL,
        user_id INT NULL, -- Can be null for anonymous, but we usually track who answered if not fully anon
        answers TEXT, -- JSON key-value pairs
        reporter_hash VARCHAR(64), -- For distinctness if anonymous
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (pulse_id) REFERENCES pulses(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Table 'pulse_responses' checked/created.\n";

    echo "Database setup completed successfully.\n";

} catch (PDOException $e) {
    die("DB Setup Error: " . $e->getMessage() . "\n");
}
