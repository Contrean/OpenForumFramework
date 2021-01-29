<?php
class Authcontrol {
    private static function checkForInvalidSessions($sessions) {
        $d = new DateTime();
        $removeSessions = [];
        for ($i=0; $i < count($sessions); $i++) { 
            $session = $sessions[$i];
            if ($session["timestamp"] < $d->getTimestamp() - 3600 * 24) {
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
        $sessions = json_decode(file_get_contents(__DIR__."/../../cache/sessions/sessions.json"), true);
        
        $sessions = self::checkForInvalidSessions($sessions);

        $sessionToken = hash("sha512", strval(count($sessions) / 2 * count($sessions))).hash("md5", strval(microtime()));
        $sessionToken = hash("whirlpool", $sessionToken);

        $d = new DateTime();
        array_push($sessions, ["token" => $sessionToken, "timestamp" => $d->getTimestamp()]);

        file_put_contents(__DIR__."/../../cache/sessions/sessions.json", json_encode($sessions));
        return $sessionToken;
    }
}