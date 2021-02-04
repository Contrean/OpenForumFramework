<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__."/phpmailer/phpmailer/src/Exception.php";
require __DIR__."/phpmailer/phpmailer/src/PHPMailer.php";
require __DIR__."/phpmailer/phpmailer/src/SMTP.php";

class MailHandler {
    public static function sendVerification($receiver) {
        $CONF = json_decode(file_get_contents(__DIR__."/../../config.json"), true)["mails"];
        $body = file_get_contents(__DIR__."/../mail/mails/verify.html");

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $CONF["SMTPserver"];
        $mail->Port = $CONF["port"];
        $mail->SMTPAuth = true;
        $mail->Username = $CONF["username"];
        $mail->Password = $CONF["password"];
        $mail->SMTPSecure = $CONF["SMTPEncryption"];

        $mail->setFrom($CONF["sender"], $CONF["displayname"]);
        $mail->addAddress($receiver);

        $mail->Subject = "Subject" /*$LANG->get('mail.verifiy_subject')*/;;
        $mail->msgHTML($body);
        $mail->AltBody = strip_tags($body);
        
        if (!$mail->send()) {
            echo $mail->ErrorInfo;
        } else {
            echo "Success";
        }
    }
}