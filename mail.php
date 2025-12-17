<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
//Load Composer's autoloader (created by composer, not included with PHPMailer)
require 'vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);
function sendMailToNewAdmin($email, $name, $subject, $body,$altbody)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['email'];
        $mail->Password = $_ENV['password_email'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($_ENV['email'], 'Admin of Journal');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body =$body;
        $mail->AltBody = $altbody;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}