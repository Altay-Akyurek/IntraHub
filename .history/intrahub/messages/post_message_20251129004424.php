<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$payload = json_decode(file_get_contents('php://input'), true);

?>