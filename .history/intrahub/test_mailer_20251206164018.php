<?php
require_once __DIR__ . '/includes/mailer.php';

try {
    send_mail("kazan17170@gmail.com
", "Test Mail", "walcome IntraHub person working ");
    echo "Mail başarıyla gönderildi!";
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage();
}
