<?php
//Scan the route for files in the public directory
$files = scandir(__DIR__);
if (in_array(str_replace("/", "", $_SERVER['REQUEST_URI']), $files)) {
    echo file_get_contents(__DIR__.$_SERVER['REQUEST_URI']);
    exit();
}

//Include languagemanagement
include __DIR__."/../app/language.php";
$LANG = new Language();

//Create request
require __DIR__."/../app/request.php";
$Request = new Request([]);

//Create a route instance
require __DIR__."/../app/kernel.php";
$ROUTE = new Route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $Request);

//Test if the route is a valid route
require __DIR__."/../route/controllers.php";
require __DIR__."/../route/public.php";

//Will be called, if no route was called
abort(404);