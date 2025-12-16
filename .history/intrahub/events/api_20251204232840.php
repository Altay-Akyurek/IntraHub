<?php
//event/api.php -FullCaledar backend (list,creat,update,delete,attend)
//JSON in/out. POST with action for create/update/delete/attend
//GET returns list of event list events for calendar.

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];


?>