<?php
require_once __DIR__ . '/includes/db.php';

try {
    // DROP DEPENDENT TABLES FIRST
    $pdo->exec("DROP TABLE IF EXISTS pulse_responses");
    $pdo->exec("DROP TABLE IF EXISTS pulses");
    $pdo->exec("DROP TABLE IF EXISTS messages");
    $pdo->exec("DROP TABLE IF EXISTS complaints");

    // PULSES
    $pdo->exec("
        CREATE TABLE pulses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            questions TEXT, -- JSON
            send_at DATETIME NULL,
            active TINYINT(1) DEFAULT 1,
            created_by INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Pulses table recreated.\n";

    // PULSE RESPONSES
    $pdo->exec("
        CREATE TABLE pulse_responses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pulse_id INT,
            user_id INT NULL,
            answers TEXT, -- JSON
            reporter_hash VARCHAR(64) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (pulse_id) REFERENCES pulses(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Pulse Responses table recreated.\n";

    // MESSAGES
    $pdo->exec("
        CREATE TABLE messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            content TEXT,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Messages table recreated.\n";

    // COMPLAINTS
    $pdo->exec("
        CREATE TABLE complaints (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            content TEXT NOT NULL,
            is_anonymous TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Complaints table recreated.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
