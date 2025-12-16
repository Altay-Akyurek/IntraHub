<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/mailer.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$info = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email === '') {
        $errors[] = 'Lütfen e-posta girin.';
    } else {
        $stmt = $pdo->prepare("SELECT id,full_name FROM users WHERE email= :e LIMIT 1");
        $stmt->execute(['e' => $email]);
        $user = $stmt->fetch();
        if (!$user) {
            $info = bin2hex(random_bytes(24));

        } else {
            $token = bin2hex(random_bytes(24));
            $expires = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

            $insert = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:uid, :t, :exp)");
            $insert->execute(['uid' => $user['id'], 't' => $token, 'exp' => $expires]);


            //sıfırlanma link Göderimi

            // Base Path Detection
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http');
            $host = $_SERVER['HTTP_HOST'];
            $path = dirname($_SERVER['SCRIPT_NAME']); // e.g. /php/company_Chat/intrahub
            
            // Clean path (remove trailing slash if exists, though dirname usually handles this)
            $path = rtrim($path, '/\\');

            $resetLink = "{$protocol}://{$host}{$path}/reset_password.php?token={$token}";


            try {
                // Plain Text Body (Fallback)
                $body = "Merhaba {$user['full_name']},\n\nIntraHub Parolanızı sıfırlamak için aşağıdaki bağlantıya tıklayınız:\n\n{$resetLink}\n\nBağlantı 1 saat geçerlidir.\n\nEğer siz talep etmediyseniz bu e-postayı görmezden gelin.";
                
                // HTML Body
                $htmlBody = "
                <div style='font-family: Arial, sans-serif; color: #333;'>
                    <h3>Merhaba {$user['full_name']},</h3>
                    <p>IntraHub parolanızı sıfırlamak için lütfen aşağıdaki butona tıklayın:</p>
                    <p style='margin: 20px 0;'>
                        <a href='{$resetLink}' style='background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Parolamı Sıfırla</a>
                    </p>
                    <p>Veya aşağıdaki bağlantıyı tarayıcınıza yapıştırın:</p>
                    <p><a href='{$resetLink}'>{$resetLink}</a></p>
                    <p><em>Bu bağlantı 1 saat süreyle geçerlidir.</em></p>
                </div>";

                send_mail($email, 'IntraHub - Parola Sıfırlama', $body, $htmlBody);
                $info = 'E-posta gönderildi. Lütfen e-postanızı (ve spam klasörünü) kontrol ediniz.';

            } catch (Exception $e) {
                $errors[] = 'E-posta göderilirken hata oluştu:' . $e->getMessage();
            }
        }
    }
}


$pageTitle = 'Şifremi Unuttum';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-container fade-in-up" style="max-width: 500px;">
    <div class="glass-card">
        <div class="text-center mb-3">
            <h2 class="navbar-brand" style="float: none; font-size: 2.5rem;">IntraHub</h2>
            <p class="text-muted" style="color: var(--text-muted)">Şifre Sıfırlama</p>
        </div>

        <?php if (!empty($errors)): ?>
             <div class="alert">
                <?php foreach ($errors as $e): ?>
                    <div><i class="fas fa-exclamation-triangle"></i> <?= e($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div class="alert" style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3); color: #6ee7b7;">
                <i class="fas fa-check-circle"></i> <?= e($info) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Kayıtlı E-Posta</label>
                <input name="email" type="email" class="form-control" placeholder="ornek@sirket.com" required>
            </div>
            <button class="btn btn-primary w-100">
                Sıfırlama Bağlantısı Gönder <i class="fas fa-paper-plane" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div class="mt-4 text-center">
             <a href="index.php" style="font-size: 0.9rem;">Giriş Yap</a> | <a href="register.php" style="font-size: 0.9rem;">Kayıt Ol</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>