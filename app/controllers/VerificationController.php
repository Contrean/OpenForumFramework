<?php
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

    public static function isValidVLink($vLink) {
        $links = json_decode(file_get_contents(__DIR__."/../../cache/sessions/verificationlinks.json"), true);
        $links = self::checkForInvalidLinks($links);

        foreach ($links as $link) {
            if ($link["link"] == $vLink) {
                return true;
            }
        }
        return false;
    }

    public static function getUserByLink($vLink) {
        $links = json_decode(file_get_contents(__DIR__."/../../cache/sessions/verificationlinks.json"), true);
        foreach ($links as $link) {
            if ($link["link"] == $vLink) {
                $key = array_search($link, $links);
                unset($links[$key]);

                file_put_contents(__DIR__."/../../cache/sessions/verificationlinks.json", json_encode($links));
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
        echo $userId;

    }
}