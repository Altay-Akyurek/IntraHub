<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/mailer.php';
session_start();

$info=null;
$errors = [];

if($_SERVER['REQUEST_METHOD']=== 'POST'){
     $email = trim($_POST['email'] ?? '');
     if($email ===''){
        $errors[]= 'Lütfen e-posta girin.';
     }else{
        $stmt= $pdo->prepare("SELECT id,full_name FROM users WHERE email= :e LIMIT 1");
        $stmt->execute(['e'=> $email]);
        $user=$stmt->fetch();
        if(!$user){
            $info=bin2hex(random_bytes(24));

        }else{
            $token = bin2hex(random_bytes(24));
            $expires = (new DateTime('+1 hour'))-> format('Y-m-d H:i:s');

            $insert = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:uid, :t, :exp)");
            $insert ->execute(['uid'=>$user['id'],'t'=>$token,'exp'=>$expires]);


            //sıfırlanma link Göderimi

            $resetLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/reset_password.php?token={$token}";


            try{
                $body= "Merhaba {$user['full_name']},\n\nIntraHub Parolanızı sıfırlamak için aşağıdaki bağlantıya tıklayınız.Bağlantı 1 saate geçerlidir.\n\nEğer siz talep etmediyseniz bu e-posta görmezden gelin";
                send_mail($email,'IntraHub-Parola Sıfırlama',$body);
                $info='E-posta gönderildi.Lütfen e-postanızdan kontrol ediniz.';

            }catch{
                $errors[]='E-posta göderilirken hata oluştu:'.$e->getMessage();
            }
        }
     }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama - IntraHub</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        
                            <h4 class="card-title"></h4>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>