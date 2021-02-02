<?php
//Scan the route for files in the public directory
$files = scandir(__DIR__);
if (in_array(str_replace("/", "", $_SERVER['REQUEST_URI']), $files)) {
    echo file_get_contents(__DIR__.$_SERVER['REQUEST_URI']);
    exit();
}

//Test wether the sessions-file is a json-file (To prevent errors)
if (gettype(json_decode(file_get_contents(__DIR__."/../cache/sessions/sessions.json"))) != "string") {
    file_put_contents(__DIR__."/../cache/sessions/sessions.json", "[]");
}

//Create Cookie Instance
require __DIR__."/../app/kernel/cookie.php";
$Cookies = new Cookies($_COOKIE);

//Include languagemanagement
include __DIR__."/../app/kernel/language.php";
$LANG = new Language();

//Create Session
require __DIR__."/../app/kernel/session.php";
$Session = (isset($Cookies->sId)) ? new Session($Cookies->sId) : new Session(0);

//Create request
require __DIR__."/../app/kernel/request.php";
$Request = new Request([]);

//Create a route instance
require __DIR__."/../app/kernel/kernel.php";
$ROUTE = new Route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $Request);

//Test if the route is a valid route
require __DIR__."/../route/controllers.php";
require __DIR__."/../route/public.php";

//Will be called, if no route was called
abort();