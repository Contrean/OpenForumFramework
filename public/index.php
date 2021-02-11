<?php
//Create Cookie Instance
require_once __DIR__."/../app/kernel/cookie.php";
$Cookies = new Cookies($_COOKIE);

//Create Session
require_once __DIR__."/../app/kernel/session.php";
$Session = (isset($Cookies->sId)) ? new Session($Cookies->sId) : new Session(0);

//Scan the route for files in the public directory
$_SERVER['REQUEST_URI'] = ($_SERVER['REQUEST_URI'] == '/index.php') ? '/' : $_SERVER['REQUEST_URI'];
if (is_file(__DIR__.$_SERVER['REQUEST_URI'])) {
    $ext = pathinfo($_SERVER['REQUEST_URI'])['extension'];
    switch ($ext) {
        case 'css':
            /*For some reason css won't be recognized as css but as plain... */
            header('Content-Type: text/css');
            break;
        
        default:
            header('Content-Type: '.mime_content_type(__DIR__.$_SERVER['REQUEST_URI']));
            break;
    }
    echo file_get_contents(__DIR__.$_SERVER['REQUEST_URI']);
    exit();
}

//Test wether the sessions-file is a json-file (To prevent errors)
if (gettype(json_decode(file_get_contents(__DIR__."/../cache/sessions/sessions.json"))) != "string") {
    file_put_contents(__DIR__."/../cache/sessions/sessions.json", "[]");
}

//Include languagemanagement
include __DIR__."/../app/kernel/language.php";
$LANG = new Language();

//Create request
require_once __DIR__."/../app/kernel/request.php";
$Request = new Request([]);

//Create a route instance
require_once __DIR__."/../app/kernel/kernel.php";
$ROUTE = new Route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $Request);

//Test if the route is a valid route
require_once __DIR__."/../route/controllers.php";
require_once __DIR__."/../route/public.php";

//Will be called, if no route was called
abort();