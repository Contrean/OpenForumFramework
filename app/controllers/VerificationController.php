<?php
require_once __DIR__."/../sql/models/User.php";

class VerificationController {
    private static function checkForInvalidLinks($links) {
        $d = new DateTime();
        $removeLinks = [];
        for ($i=0; $i < count($links); $i++) {
            $link = $links[$i];
            if ($link["timestamp"] < $d->getTimestamp() - 600) {
                array_push($removeLinks, $i);
            }
        }
        rsort($removeLinks);
        foreach ($removeLinks as $index) {
            array_splice($links, $index, 1);
        }
        return $links;
    }

    public static function createVerificationlink($userId) {
        $links = json_decode(file_get_contents(__DIR__."/../../cache/sessions/verificationlinks.json"), true);
        $links = self::checkForInvalidLinks($links);

        $link = hash("crc32", strval(count($links)).strval(microtime(false)));
        $link = hash("md5", $link);

        $d = new DateTime();

        array_push($links, ["link" => $link, "timestamp" => $d->getTimestamp(), "userId" => $userId]);

        file_put_contents(__DIR__."/../../cache/sessions/verificationlinks.json", json_encode($links));
        return $link;
    }

    public static function createResetlink($userId) {
        $links = json_decode(file_get_contents(__DIR__."/../../cache/sessions/resetlinks.json"), true);
        $links = self::checkForInvalidLinks($links);

        $link = hash("crc32", strval(count($links)).strval(microtime(false) / M_PI));
        $link = hash("md5", $link);

        $d = new DateTime();

        array_push($links, ["link" => $link, "timestamp" => $d->getTimestamp(), "userId" => $userId]);

        file_put_contents(__DIR__."/../../cache/sessions/resetlinks.json", json_encode($links));
        return $link;
    }

    public static function isValidVLink($vLink, $mode = "v") {
        $file = ($mode == "v") ? __DIR__."/../../cache/sessions/verificationlinks.json" : __DIR__."/../../cache/sessions/resetlinks.json";
        $links = json_decode(file_get_contents($file), true);
        $links = self::checkForInvalidLinks($links);

        foreach ($links as $link) {
            if ($link["link"] == $vLink) {
                return true;
            }
        }
        return false;
    }

    public static function getUserByLink($vLink, $mode = "v") {
        $file = ($mode == "v") ? __DIR__."/../../cache/sessions/verificationlinks.json" : __DIR__."/../../cache/sessions/resetlinks.json";
        $links = json_decode(file_get_contents($file), true);
        foreach ($links as $link) {
            if ($link["link"] == $vLink) {
                $key = array_search($link, $links);
                unset($links[$key]);

                file_put_contents($file, json_encode($links));
                return $link["userId"];
            }
        }
        return null;
    }

    public static function verifyUser($Request) {
        $vLink = $Request->vLink;

        if (!self::isValidVLink($vLink)) {
            abort();
        }

        $userId = self::getUserByLink($vLink);

        $User = new User();
        $User->update(["isVerified"], [1]);
        $User->where("userId", "=", $userId);
        $User->execute();
    }

    public static function resetPassword($Request) {
        $vLink = $Request->vLink;

        if (!self::isValidVLink($vLink, "r")) {
            abort();
        }

        $userId = self::getUserByLink($vLink, "r");
        
        view("templates/resetPassword.php");
    }
}