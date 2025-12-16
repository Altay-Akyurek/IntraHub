<?php
require_once __DIR__ . '/includes/mail.php';

try {
    send_mail("kendi_gmail_adresin@gmail.com", "Test Mail", "Mail gönderme testi başarılı ✔️");
    echo "Mail başarıyla gönderildi!";
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage();
}
