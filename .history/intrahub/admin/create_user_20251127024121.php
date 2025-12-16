<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role = in_array($_POST['role'] ?? '', ['admin', 'employee']) ? $_POST['role'] : 'employee';
    $department = trim($_POST['department'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $email === '' || $password === '') {
        flash_set('error', 'Kullanıcı adı, e-posta ve şifre zorunludur.');

    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username,email,password,full_name,role,department) VALUES (:username,:email,:password.:full_name,:role.:department)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'full_name' => $full_name,
                'role' => $role,
                'department' => $department,
            ]);
            $newId = $pdo->lastInsertId();
            flash_set('success', 'Kullanıcı oluşturuldu (ID:' . $newId . ')');


            //opsiyonel hoşgeldin mesajı
            try {
                send_mail($email, 'IntraHub Hesabınız Oluşturuldu', "Merhaba {$full_name},\n\nHesabınız oluşturuldu. Kullanıcı adı: {$username}\n");
            } catch (Exception $e) {
                //mail başarısız göderim  olsa bile kullanıcı oluyşturuldu
                flash_set('info', 'Kullanıcı oluşturuldu ancak e-posta gönderilemedi: ' . $e->getMessage());
            }
        }
    }
}
?>