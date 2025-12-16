<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Force session check
if (session_status() === PHP_SESSION_NONE) session_start();

$user = $_SESSION['user'] ?? null;
$userId = $user['id'] ?? 0;

$otherUsers = [];
if ($userId) {
    $stmt = $pdo->query("SELECT id, username FROM users WHERE id != $userId LIMIT 10");
    $otherUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle Test Post
$msgResult = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_msg'])) {
    $to = intval($_POST['to']);
    $txt = trim($_POST['content']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $to, $txt]);
        $msgResult = "Success! Message ID: " . $pdo->lastInsertId();
    } catch (Exception $e) {
        $msgResult = "Error: " . $e->getMessage();
    }
}

// Fetch Last Messages
$lastMessages = [];
if ($userId) {
    $stmt = $pdo->query("SELECT * FROM messages WHERE sender_id = $userId OR receiver_id = $userId ORDER BY id DESC LIMIT 10");
    $lastMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Messaging Diagnostic</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f0f0f0; }
        .section { background: white; padding: 15px; margin-bottom: 20px; border: 1px solid #ccc; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Intrahub Messaging Diagnostic</h1>

    <div class="section">
        <h2>1. Session Status</h2>
        <?php if ($user): ?>
            <div class="success">Logged In as ID: <?= $userId ?> (<?= $user['username'] ?>)</div>
        <?php else: ?>
            <div class="error">NOT LOGGED IN. <a href="../login.php">Login here</a></div>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>2. Database Connection</h2>
        <?php
        if ($pdo) echo "<div class='success'>Connected to DB.</div>";
        else echo "<div class='error'>DB Connection FAILED.</div>";
        ?>
    </div>

    <?php if ($userId): ?>
    <div class="section">
        <h2>3. Test Sending Message</h2>
        <?php if ($msgResult): ?>
            <div style="padding: 10px; background: #eee; border-left: 5px solid #333; margin-bottom: 10px;"><?= $msgResult ?></div>
        <?php endif; ?>
        
        <form method="post">
            Send to: 
            <select name="to">
                <?php foreach ($otherUsers as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= $u['username'] ?> (ID: <?= $u['id'] ?>)</option>
                <?php endforeach; ?>
            </select><br><br>
            Content: <input type="text" name="content" value="Test Message <?= date('H:i:s') ?>" required>
            <button type="submit" name="test_msg">Send Direct SQL</button>
        </form>
    </div>

    <div class="section">
        <h2>4. Last 10 Messages (DB View)</h2>
        <table border="1" cellpadding="5" style="width:100%; border-collapse: collapse;">
            <tr>
                <th>ID</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Content</th>
                <th>Time</th>
            </tr>
            <?php foreach ($lastMessages as $m): ?>
                <tr>
                    <td><?= $m['id'] ?></td>
                    <td><?= $m['sender_id'] ?></td>
                    <td><?= $m['receiver_id'] ?></td>
                    <td><?= htmlspecialchars($m['content']) ?></td>
                    <td><?= $m['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

</body>
</html>
