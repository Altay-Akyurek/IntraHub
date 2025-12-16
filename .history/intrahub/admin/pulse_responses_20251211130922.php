<?php
require_once __DIR__ .'/../includes/auth.php';
require_role('admin');
require_once __DIR__ .'/../includes/db.php';
require_once __DIR__ .'/../includes/functions.php';


$id= intval($_GET['id'] ?? 0);
if(!$id){
    flash_set('error', 'Eksik pulse id');
    header('Location: /admin/pulses.php');
    exit

}
?>