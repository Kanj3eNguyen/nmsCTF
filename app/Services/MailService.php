<?php
namespace App\Services;
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    public static function sendOtpEmail($toEmail, $resetLink)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'] ?? ''; 
            $mail->Password   = $_ENV['SMTP_PASSWORD'] ?? ''; 
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $_ENV['SMTP_PORT'] ?? 587;

            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'] ?? 'quangcoc69@gmail.com', $_ENV['MAIL_FROM_NAME'] ?? 'nmsCTF');
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Reset password request';
            $mail->Body    = "Bạn đã yêu cầu khôi phục mật khẩu. Vui lòng click vào link dưới đây để tiếp tục: <a href='{$resetLink}'>Khôi phục mật khẩu</a>. Link này sẽ hết hạn trong 30 phút.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}