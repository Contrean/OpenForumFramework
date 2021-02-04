<?php
require __DIR__."/../mail/mailhandler.php";
class MailController {
    public static function sendVerification($Request) {
        MailHandler::sendVerification($Request->receiver, $Request->receiverId);
    }
}