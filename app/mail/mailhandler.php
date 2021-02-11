<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__."/phpmailer/phpmailer/src/Exception.php";
require_once __DIR__."/phpmailer/phpmailer/src/PHPMailer.php";
require_once __DIR__."/phpmailer/phpmailer/src/SMTP.php";

require_once __DIR__."/../controllers/VerificationController.php";
require_once __DIR__."/../kernel/language.php";

class MailHandler {

    public static function sendVerification($receiver, $userId) {
        $CONF = json_decode(file_get_contents(__DIR__."/../../config.json"), true)["mails"];
        $LANG = new Language();
        
        $link = VerificationController::createVerificationlink($userId);
        $body = str_replace("%LINK%", $link, file_get_contents(__DIR__."/../mail/mails/verify.html"));

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $CONF["SMTPserver"];
        $mail->Port = $CONF["port"];
        $mail->SMTPAuth = true;
        $mail->Username = $CONF["username"];
        $mail->Password = $CONF["password"];
        $mail->SMTPSecure = $CONF["SMTPEncryption"];

        $mail->setFrom($CONF["username"], $CONF["displayname"]);
        $mail->addAddress($receiver);

        $mail->Subject = $LANG->get('mail.verify_subject');
        $mail->msgHTML($body);
        $mail->AltBody = strip_tags($body);
        
        if (!$mail->send()) {
            echo $mail->ErrorInfo;
        }
    }

    public static function sendPasswordReset($receiver, $userId) {
        $CONF = json_decode(file_get_contents(__DIR__."/../../config.json"), true)["mails"];
        $LANG = new Language();

        $link = VerificationController::createResetlink($userId);
        $body = str_replace("%LINK%", $link, file_get_contents(__DIR__."/../mail/mails/reset.html"));

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $CONF["SMTPserver"];
        $mail->Port = $CONF["port"];

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->Username = $CONF["username"];
        $mail->Password = $CONF["password"];
        $mail->SMTPSecure = $CONF["SMTPEncryption"];

        $mail->setFrom($CONF["username"], $CONF["displayname"]);
        $mail->addAddress($receiver);

        $mail->Subject = $LANG->get('mail.reset_subject');
        $mail->msgHTML($body);
        $mail->AltBody = strip_tags($body);
        
        if (!$mail->send()) {
            echo $mail->ErrorInfo;
        } else {
            echo "Success";
        }
    }
}