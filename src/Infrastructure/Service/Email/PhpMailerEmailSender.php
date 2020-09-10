<?php
declare(strict_types=1);

namespace App\Infrastructure\Service\Email;


use App\Application\Exceptions\AppException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PhpMailerEmailSender implements EmailSenderInterface

{
    private EmailServiceSettings $mailerSettings;

    public function __construct(EmailServiceSettings $settings)
    {
        $this->mailerSettings = $settings;
    }

    public function send(string $to, string $subject, string $body): void
    {
        try {
            $mail = $this->initMailer($this->mailerSettings);
            $mail->setFrom('bares.informer@brestenergo.by', 'Оповещатель Брестэнерго');
            $mail->addAddress($to);
            // Content
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = ''; //This is the body in plain text for non-HTML mail clients

            $mail->send();
        } catch (Exception $e) {
            throw new AppException("письмо не отправлено, ошибка при отправке: {$e->errorMessage()}", 0, $e);
        }
    }

    private function initMailer(EmailServiceSettings $settings): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Encoding = PHPMailer::ENCODING_BASE64;
        $mail->setLanguage('ru');
        $mail->isSMTP();
        $mail->Host = $settings->host;
        $mail->SMTPAuth = $settings->SMTPAuth;
        $mail->Username = $settings->username;
        $mail->Password = $settings->password;
        $mail->Port = $settings->port;
        return $mail;
    }

}