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
               <h3>Reset Your Password</h3>
                <p>Hello,</p>
                <p>We received a request to reset the password for your account associated with this email address. If you did not request a password reset, please ignore this message.</p>

                <p>To reset your password, click the button below:</p>
                <p>
                    <a href='{$resetLink}' style='color:#0d6efd; text-decoration:underline; font-family:Arial, sans-serif;'>
                    Click here to reset your password
                    </a>
                </p>

                <p>This link is valid for <b>30 minutes</b>. After that, you will need to request a new password reset link.</p>

                <p>For your security, please do not share this link with anyone.</p>

                <p>If you did not initiate this request, you can safely ignore this email and your account will remain secure.</p>

                <p>Thank you for using <b>" . APP_NAME . "</b>.</p>

                <p>Best regards,<br>
                The " . APP_NAME . " Team</p>
                ";

            return $mail->send();

        } catch (Exception $e) {
            error_log($mail->ErrorInfo);
            return false;
        }
    }
}
