<?php
require_once __DIR__."/../controllers/Authcontrol.php";

class Session {
    public $sessionId;
    function __construct($sessionId) {
        $this->sessionId = Authcontrol::createNewSessionIfInvalid($sessionId);
        setcookie("sId", $this->sessionId, time() + 2592000, "/");
    }
}