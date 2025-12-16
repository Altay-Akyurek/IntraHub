<?php
require_once __DIR__ . '/intrahub/includes/mail.php';

try {
    send_mail("kazan17170@gmail.com", "Test", "Mail başarıyla gönderildi ✔️");
    echo "Mail gönderildi!";
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage();
}
