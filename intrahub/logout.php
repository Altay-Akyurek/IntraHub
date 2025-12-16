<?php
require_once __DIR__ . '/includes/auth.php';
logout();
header('Location: ../intrahub/index.php');
exit;
?>