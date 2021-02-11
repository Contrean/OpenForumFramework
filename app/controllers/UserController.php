<?php
require_once __DIR__."/../sql/models/User.php";
require_once __DIR__."/Authcontrol.php";
require_once __DIR__."/MailController.php";
require_once __DIR__."/../kernel/request.php";

class UserController {
    public static function resetPassword($Request) {
        $pw1 = $Request->pw1;
        $pw2 = $Request->pw2;
        $v;
        $userId = $Request->userId;

        if ($pw1 != $pw2) {
            echo "E:PW";
            exit();
        }

        $Users = new User();
        if ($Users->isRecord("userId", intval($userId))) {
            $Users->update(["password"], [hash("sha512", $pw1)]);
            $Users->where("userId", "=", intval($userId));
            $Users->execute();
            echo "S:PC";
            exit();
        } else {
            echo "E:UI";
            exit();
        }
        
    }

    private static function usernameTaken($username) {
        $Users = new User();
        if ($Users->isRecord("username", $username)) {
            return true;
        }
        return false;
    }

    private static function emailTaken($email) {
        $Users = new User();
        if ($Users->isRecord("email", $email)) {
            return true;
        }
        return false;
    }

    public static function createUser($Request) {
        if (!isset($Request->username)) {
            echo "E:MU";
            exit();
        }
        if (!isset($Request->email)) {
            echo "E:ME";
            exit();
        }

        $pw1 = $Request->pw1;
        $pw2 = $Request->pw2;

        if ($pw1 != $pw2) {
            echo "E:PW";
            exit();
        }

        $username = $Request->username;
        if (self::usernameTaken($username)) {
            echo "E:UT";
            exit();
        }

        $email = $Request->email;
        
        if (self::emailTaken($email)) {
            echo "E:ET";
            exit();
        }

        $password = hash("sha512", $pw1);

        $Users = new User();
        $Users->insert(["username", "password", "email"], [$username, $password, $email]);
        $Users->execute();
        $Users->get("userId");
        $Users->where("username", "=", $username);
        $Users->where("email", "=", $email);
        $Users->where("password", "=", $password);
        $userId = $Users->fetch("assoc", $Users->execute())["userId"];
        
        $Request = new Request(["receiver" => $email, "receiverId" => $userId]);
        MailController::sendVerification($Request);

        echo "S:SV";
        exit();
    }

    public static function changeUserinfo($Request) {
        $userId = Authcontrol::getUIBySID($Cookies->sId);
        if ($userId != 0) {
            $Users = new User();
            $Users->update(["username", "email"], [$Request->username, $Request->email]);
            $Users->where("userId", "=", $userId);
            $Users->execute();
            echo "S:CU";
        }
        echo "E:IS";
    }
}