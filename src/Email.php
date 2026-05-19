<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email
{
    private string $reciver;
    private  string $sender = 'manzikchris2@gmail.com';
    private  string $pass = 'yxmo fdks shjh podz';
    private PHPMailer $mail;
    private PDO $conn;
    public function __construct(string $email)
    {
        $this->reciver = $email;
        $this->mail = new PHPMailer(true);
    }
    private function configureSMTP(): void
    {
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $this->sender;
        $this->mail->Password = $this->pass;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->setFrom($this->sender, 'D DELIVERY');
        $this->mail->addAddress($this->reciver);
        $this->mail->isHTML(true);
    }
    private function genarateSecureOtp(int $numbers)
    {
        $otp = '';
        for ($i = 0; $i < $numbers; $i++) {
            $otp .= random_int(0, 9);
        }
        return $otp;
    }
    public function sendotp(PDO $db, string $attr)
    {
        $this->conn = $db;
        try {
            $numericOTP = $this->genarateSecureOtp(6);
            $this->configureSMTP();
            $this->mail->Subject = 'OTP CONFIRMATION';
            $this->mail->Body = '<html>
        <body style="font-family: Arial, Helvetica, sans-serif;">
            <div style="max-width: 500px; margin: 0 auto; padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 10px;">
                <h1 style="color: #333;">OTP CONFIRMATION</h1>
                <p style="font-size: 16px; color: #666;">Insert this OTP to complete your login</p>
                <div style="background-color: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="font-size: 32px; font-weight: bold; color: #4CAF50; letter-spacing: 3px; margin: 0;">' . $numericOTP . '</p>
                </div>
                <p style="color: #ff6b6b; font-size: 14px;"> This OTP will expire in 1 minute</p>
                <hr style="margin: 20px 0;">
                <p style="font-size: 12px; color: #999;">This is an automated message, please do not reply.</p>
            </div>
        </body>
        </html>';




            $this->mail->AltBody = "OTP CONFIRMATION\n\n";
            $this->mail->AltBody .= "Hello,\n\n";
            $this->mail->AltBody .= "Please use the following OTP to complete your login:\n\n";
            $this->mail->AltBody .= "OTP Code: {$numericOTP}\n\n";
            $this->mail->AltBody .= "This OTP will expire in 1 minute.\n";
            $this->mail->AltBody .= "For security reasons, do not share this OTP with anyone.\n\n";
            $this->mail->AltBody .= "This is an automated message, please do not reply.\n";




            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare('UPDATE Users SET otp = :otp,ot_time = :timenow  WHERE Email = :email and attribute=:attr');
            $stmt->execute([':otp' => $numericOTP, ':email' => $this->reciver, ':timenow' => time(), 'attr' => $attr]);
            $this->conn->commit();
            if (!$this->mail->send()) {
                throw new Exception("Email sending failed: " . $this->mail->ErrorInfo);
            }
            return true;
        } catch (Exception $th) {
            $_SESSION['Email_error'] = $this->mail->ErrorInfo;
            $_SESSION['PDO_error'] = [
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ];
            return false;
        }
    }
    public function sendaccept(string $html)
    {
        try {
            $this->configureSMTP();
            $this->mail->Subject = 'order delivery';
            $this->mail->Body = '<html>
                             <body style="font-family: Arial, Helvetica, sans-serif;">
            <div style="max-width: 500px; margin: 0 auto; padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 10px;">
                <h1 style="color: rgb(114,214,242);">ORDER DELIVERY</h1>
                <p style="font-size: 16px; color: #666;">arider has accepted your order</p>
                <div style="background-color: black; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    ' . $html . '
                </div>
                <p style="font-size: 12px; color: #999;">This is an automated message, please do not reply.</p>
            </div>
        </body>
                             </html>';
            $this->mail->send();
            return true;
        } catch (Exception $th) {
            $_SESSION['Email_error'] = $this->mail->ErrorInfo;
            $_SESSION['Email_issue'] = [$this->reciver, $th->getFile(), $th->getMessage(), $th->getLine()];
            return false;
        }
    }
    public function sendreset()
    {
        try {
            $userId = $this->reciver;
            $userType = 'customer';
            $origin = 'http://localhost:80'; // or use $_SERVER['HTTP_HOST']
            $encodedId = urlencode($userId);
            $encodedType = urlencode($userType);

            $this->configureSMTP();
            $this->mail->Subject = 'Password Reset Request';
            $this->mail->Body = "<h1>Password Reset</h1>
                                 <p>Click the link below to reset your password:</p>
                                 <a href='{$origin}/reset/{$encodedType}/{$encodedId}'>Reset Password</a>
                                 <p>This link expires in 1 hour.</p>";

            $this->mail->send();
            return 'sent';
        } catch (Exception $e) {
            return $_SESSION['Email_error'] = $this->mail->ErrorInfo;
        }
    }
    public function sendPaccept(string $html)
    {
        try {
            $this->configureSMTP();
            $this->mail->Subject = 'order delivery';
            $this->mail->Body = '<html>
                             <body style="font-family: Arial, Helvetica, sans-serif;">
            <div style="max-width: 500px; margin: 0 auto; padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 10px;">
                <h1 style="color: rgb(114,214,242); text-transform:uppercase">' . $html . ' HAS ACCPTED</h1>
               ' . $html . '
                <p style="font-size: 12px; color: #999;">This is an automated message, please do not reply.</p>
            </div>
        </body>
                             </html>';
            $this->mail->send();
            return true;
        } catch (Exception $th) {
            $_SESSION['Email_error'] = $this->mail->ErrorInfo;
            $_SESSION['Email_issue'] = [$this->reciver, $th->getFile(), $th->getMessage(), $th->getLine()];
            return false;
        }
    }
    public function sendPdone(string $html)
    {
        try {
            $this->configureSMTP();
            $this->mail->Subject = 'order delivery';
            $this->mail->Body = '<html>
                             <body style="font-family: Arial, Helvetica, sans-serif;">
            <div style="max-width: 500px; margin: 0 auto; padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 10px;">
                <h1 style="color: rgb(114,214,242); text-transform:uppercase">YOUR ORDER FROM ' . $html . ' IS READY </h1>
               ' . $html . '
                <p style="font-size: 12px; color: #999;">This is an automated message, please do not reply.</p>
            </div>
        </body>
                             </html>';
            $this->mail->send();
            return true;
        } catch (Exception $th) {
            $_SESSION['Email_error'] = $this->mail->ErrorInfo;
            $_SESSION['Email_issue'] = [$this->reciver, $th->getFile(), $th->getMessage(), $th->getLine()];
            return false;
        }
    }
}
