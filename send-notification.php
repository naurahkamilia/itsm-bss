<?php 
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$emailPenerima = $_GET['email'] ?? '';
$subject       = $_GET['subject'] ?? 'Notifikasi ITSM';
$title         = $_GET['title'] ?? 'Pemberitahuan';
$pesanRaw      = urldecode($_GET['message'] ?? '');

// EMAIL PENGIRIM SEBENARNYA USER YANG LOGIN
$emailPengirim = $_SESSION['email'] ?? '';
$namaPengirim  = $_SESSION['user_name'] ?? '';

if (empty($emailPenerima)) {
    die("Email penerima kosong!");
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'nawrccy@gmail.com';
    $mail->Password   = 'qxdmqmkafvgcdusl';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // SMTP Gmail HARUS pakai akun pengirim ini
    $mail->setFrom('nawrccy@gmail.com', 'ITSM System');

    // USER LOGIN MASUK SEBAGAI Reply-To
    if (!empty($emailPengirim)) {
        $mail->addReplyTo($emailPengirim, $namaPengirim);
    }

    // ADMIN PENERIMA
    $mail->addAddress($emailPenerima);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = "<h3>$title</h3><p>$pesanRaw</p>";

    $mail->send();
    echo "Email berhasil dikirim ke {$emailPenerima}<br>";

} catch (Exception $e) {
    echo "Gagal kirim ke {$emailPenerima}: {$mail->ErrorInfo}<br>";
}
