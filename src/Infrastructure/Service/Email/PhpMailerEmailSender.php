<?php
declare(strict_types=1);

namespace App\Infrastructure\Service\Email;


use App\Application\Exceptions\AppException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PhpMailerEmailSender implements EmailSenderInterface
{
    /**
     * @param string $to
     * @param string $subject
     * @param string $body
     * @throws AppException
     */
    public function send(string $to, string $subject, string $body): void
    {
        try {
            /** @var PHPMailer $mail */
            $mail = $this->initMailer();
            $mail->setFrom('bares.informer@brestenergo.by', 'Оповещатель Брестэнерго');
            $mail->addAddress($to);
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = ''; //This is the body in plain text for non-HTML mail clients

            $mail->send();
        } catch (Exception $e) {
            throw new AppException("письмо не отправлено, ошибка отправщика: {$mail->ErrorInfo}", 0, $e);
        }
    }

    private function initMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->setLanguage('ru');
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host = 'mail.brest.energo.net';                // Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $mail->Username = 'bares.informer';                       // SMTP username
        $mail->Password = 'v39BVY';                               // SMTP password
        $mail->Port = 25;                                     // TCP port to connect to
    }

}