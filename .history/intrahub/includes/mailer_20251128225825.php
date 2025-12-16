<?php 
//Basit PHPMailer wrapper - composer autoload ve dotenv 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailler\PHPMailer\Exception;

require_once __DIR__ .'/../vendor/autoload.php';    

if(file_exists(__DIR__.'/../.env')){
    $dotenv = Dotenv/Dotenv::createImmutable(__DIR__.'/..');
    $dotenv->safeLoad();
}

/*  */
/* send_mail -basit gönderim */
/* @param string $to */
/* @param string $subject */
/* @param string $body(plain text) */
/* @param string @alt (html optinal) */

function send_mail($to,$subject,$body,$alt= null) {
    $mail =new PHPMailer(true);
    //config  from env
    $mailHost= getenv('MAIL_HOST') ? : '';
    $mailUser=getenv('MAIL_USER') ? : '';;
    $mailPass=getenv('MAIL_PASS') ? : '';
    $mailPort=
}



?>