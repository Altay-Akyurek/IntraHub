<?php
require_once __DIR__ . '/includes/mailer.php';

try {
    send_mail("kazan17170@gmail.com
", "Test Mail", "Mail gönderme testi başarılı ✔️");
    echo "Mail başarıyla gönderildi!";
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage();
}
