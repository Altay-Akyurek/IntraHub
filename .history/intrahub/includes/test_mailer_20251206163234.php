<?php
require_once __DIR__ . '/includes/mail.php';

try {
    send_mail("kendi_gmail_adresin@gmail.com", "Test", "Mail başarıyla gönderildi ✔️");
    echo "Mail gönderildi!";
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage();
}
