<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

/**
 * Basit mail gönderim fonksiyonu
 */
function send_mail(string $to, string $subject, string $body, ?string $alt = null): bool
{
    $mail = new PHPMailer(true);

    try {
        // SMTP Ayarları (.env'den yüklenir)
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['MAIL_USER'];
        $mail->Password = $_ENV['MAIL_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS
        $mail->Port = (int) $_ENV['MAIL_PORT'];

        // Gönderici
        $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
        $mail->addAddress($to);

        // İçerik
        $mail->Subject = $subject;
        $mail->isHTML(true);

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
        echo "<b>E-posta gönderilemedi:</b> " . $mail->ErrorInfo;
        return false;
    }
}
