<?php
// Basit PHPMailer wrapper - composer autoload ve dotenv kullanır
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';


if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

/**
 * send_mail - basit gönderim
 * @param string $to
 * @param string $subject
 * @param string $body (plain text)
 * @param string $alt (html optional)
 */
function send_mail($to, $subject, $body, $alt = null)
{
    $mail = new PHPMailer(true);
    // config from env
    $mailHost = getenv('MAIL_HOST') ?: '';
    $mailUser = getenv('MAIL_USER') ?: '';
    $mailPass = getenv('MAIL_PASS') ?: '';
    $mailPort = getenv('MAIL_PORT') ?: 587;
    $mailFrom = getenv('MAIL_FROM') ?: 'noreply@localhost';
    $mailFromName = getenv('MAIL_FROM_NAME') ?: 'IntraHub';

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = $mailHost;
        $mail->SMTPAuth = true;
        $mail->Username = $mailUser;
        $mail->Password = $mailPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $mailPort;

        //Recipients
        $mail->setFrom($mailFrom, $mailFromName);
        $mail->addAddress($to);

        // Content
        $mail->isHTML((bool) $alt);
        $mail->Subject = $subject;
        if ($alt) {
            $mail->Body = $alt;
            $mail->AltBody = $body;
        } else {
            $mail->Body = nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));
            $mail->AltBody = $body;
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        throw new Exception("Mail gönderilemedi: {$mail->ErrorInfo}");
    }
}