<?php
defined('APP_ACCESS') or die('Direct access not permitted');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';

class Mailer {

    public static function sendResetPassword($toEmail, $resetLink) {

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'nawrccy@gmail.com';
            $mail->Password   = 'qxdmqmkafvgcdusl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('nawrccy@gmail.com', 'ITSM System');
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Password ITSM';

            $mail->Body = "
                <h3>Reset Password</h3>
                <p>Kami menerima permintaan reset password akun Anda.</p>
                <p>
                    <a href='{$resetLink}'
                       style='background:#0d6efd;color:#fff;
                              padding:10px 20px;
                              text-decoration:none;
                              border-radius:5px;'>
                       Reset Password
                    </a>
                </p>
                <p>Link ini berlaku selama <b>30 menit</b>.</p>
                <p>Jika bukan Anda, abaikan email ini.</p>
            ";

            return $mail->send();

        } catch (Exception $e) {
            error_log($mail->ErrorInfo);
            return false;
        }
    }
}
