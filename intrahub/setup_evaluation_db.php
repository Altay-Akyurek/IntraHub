<?php
require_once __DIR__ . '/includes/db.php';

try {
    echo "Setting up evaluations table...\n";

    // Evaluations table
    $pdo->exec("CREATE TABLE IF NOT EXISTS evaluations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        rating INT NOT NULL, -- 1 to 5
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Table 'evaluations' checked/created.\n";
    
    echo "Database setup completed successfully.\n";

} catch (PDOException $e) {
    die("DB Setup Error: " . $e->getMessage() . "\n");
}
