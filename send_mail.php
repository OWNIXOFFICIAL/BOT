<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ownixofficial@gmail.com'; // your Gmail
    $mail->Password   = 'ikkh vkzq klqz ihrm'; // your Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('ownixofficial@gmail.com', 'Ownix');
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;

    $mail->send();
} catch (Exception $e) {
    echo "Failed to send code: {$mail->ErrorInfo}";
}
