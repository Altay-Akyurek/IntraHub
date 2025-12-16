<?php
require_once __DIR__ . '/intrahub/includes/db.php';
echo "Default Timezone: " . date_default_timezone_get() . "\n";
echo "Current Time: " . date('Y-m-d H:i:s') . "\n";
