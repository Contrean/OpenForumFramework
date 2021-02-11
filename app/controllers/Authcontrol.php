<?php
class Authcontrol {
    private const file = __DIR__."/../../cache/sessions/sessions.json";

    private static function checkForInvalidSessions($sessions) {
        $d = new DateTime();
        $removeSessions = [];
        for ($i=0; $i < count($sessions); $i++) { 
            $session = $sessions[$i];
            if (($session["timestamp"] < $d->getTimestamp() - 3600 * 24) && ($session["rp"] == "false")) {
                array_push($removeSessions, $i);
            }
        }
        rsort($removeSessions);
        foreach ($removeSessions as $index) {
            array_splice($sessions, $index, 1);
        }
        return $sessions;
    }

    public static function createSession() {
        $sessions = json_decode(file_get_contents(self::file), true);
        
        $sessions = self::checkForInvalidSessions($sessions);

        $sessionToken = hash("sha512", strval(count($sessions) / 2 * count($sessions))).hash("md5", strval(microtime()));
        $sessionToken = hash("whirlpool", $sessionToken);

        $d = new DateTime();
        array_push($sessions, ["token" => $sessionToken, "timestamp" => $d->getTimestamp(), "rp" => "false", "ui" => 0]);

        file_put_contents(self::file, json_encode($sessions));
        return $sessionToken;
    }

    public static function checkSessionId($sessionId) {
        if ($sessionId == 0 || strlen($sessionId) < 128) {return false;}
        $sessions = json_decode(file_get_contents(self::file), true);
        
        $sessions = self::checkForInvalidSessions($sessions);
        foreach ($sessions as $session) {
            if ($sessionId == $session["token"]) {                
                return true;
            }
        }
        return false;
    }

    public static function createNewSessionIfInvalid($sessionId) {
        if (!self::checkSessionId($sessionId)) {
            return self::createSession();
        }
        return $sessionId;
    }

    public static function isRPSession($sessionId) {
        $sessions = json_decode(file_get_contents(self::file), true);

        foreach($sessions as $session) {
            if ($session["token"] == $sessionId && $session["rp"] == "true") {
                return true;
            }
        }

        return false;
    }

    public static function makeSessionRP($sessionId) {
        $sessions = json_decode(file_get_contents(self::file), true);

        foreach($sessions as $session) {
            if ($session["token"] == $sessionId) {
                $session["rp"] = "true";
            }
        }

        file_put_contents(self::file, json_encode($sessions));
    }

    public static function deleteSession($sessionId) {
        $sessions = json_decode(file_get_contents(self::file), true);

        foreach($sessions as $session) {
            if ($session["token"] == $sessionId) {
                array_splice($sessions, array_keys($sessions, $session), 1);
                break;
            }
        }

        file_put_contents(self::file, json_encode($sessions));
    }

    public static function getUIBySID($sId) {
        $sessions = json_decode(file_get_contents(self::file), true);
        if (self::checkSessionId($sId)) {
            foreach ($sessions as $session) {
                if ($session["token"] == $sId) {
                    return $session["ui"];
                }
            }
        } else {
            return 0;
        }
    }
}