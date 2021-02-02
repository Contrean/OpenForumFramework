<?php
/**
 * Tests if a string starts with a specific substring
 */
function strStartsWith($haystack, $needle) {
    $length = strlen($needle);
    return substr($haystack, 0, $length) === $needle;
}

/**
 * Tests if a string ends with a specific substring
 */
function strEndsWith($haystack, $needle) {
    $length = strlen($needle);
    if (!$length) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}

/**
 * Throw an internal server error, that happened because a error couldn't be displayed, thanks to an error
 */
function error_500($code, $text, $file, $line) {
    echo "<h1>500 - Internal Server Error</h1><h2>Tried to throw error but errorpage couldn't be found or produced error.</h2><h3>$text</h3>";
    exit();
}

/**
 * Escape with an HTTP-Error-Code that shows an errorpage
 */
function abort($code = 404) {
    global $LANG;
    set_error_handler("error_500");
    include(__DIR__."/errorpages/$code.php");
    exit();
}

/**
 * Include a file in the view-directory
 */
function view($file) {
    global $LANG;
    global $Request;
    $dir = __DIR__."/../view/";
    $files = scandir($dir);
    if (in_array($file, $files)) {
        require("$dir$file");
    } else {
        abort();
    }
}

/**
 * Execute a controllerfunction
 */
function process($function) {
    global $Request;
    global $LANG;
    $dir = __DIR__."/../app/controllers/";
    $files = scandir($dir);

    $classname = explode("::", $function)[0];

    $classExists = false;
    foreach ($files as $file) {
        if (strEndsWith($file, ".php")) {
            require __DIR__."/controllers/$file";
            
            if (class_exists($classname)) {
                $classExists = true;
                try {
                    call_user_func($function, $Request);
                } catch (\Throwable $th) {
                    echo "Trouble finding function $function in /app/controllers/$file. Either there is a classnameconflict or the called class doesn't have this function";
                }
            }
        }
    }
    if (!$classExists) {
        echo "Trouble finding class $classname in /app/controllers/.";
    }

}

class Route {
    private $route, $method;
    public $request;

    function __construct($ROUTE, $METHOD, $REQUEST) {
        $this->uri = $ROUTE;
        $this->method = $METHOD;
        $this->request = $REQUEST;
    }

    public function get($route, $executeable) {
        if ($this->method == "GET") {
            $realRoute = explode("/", $this->uri);
            $targetRoute = explode("/", $route);
            if ($route == '/') {
                $targetRoute == [""];
            }

            $parameters = [];

            if (count($realRoute) != count($targetRoute)) {
                return false;
            } else {
                for ($i=0; $i < count($realRoute); $i++) { 
                    $realElement = $realRoute[$i];
                    $targetElement = $targetRoute[$i];

                    if (strStartsWith($targetElement, "[") && strEndsWith($targetElement, "]")) {
                        $parameters[str_replace("[", "", str_replace("]", "", $targetElement))] = $realElement;
                    } else {
                        if ($realElement != $targetElement) {
                            return false;
                        }
                    }
                }
                $this->request->add($parameters);
                $executeable();
                exit();
            }
        }

    }

    public function post($route, $executeable) {
        if ($this->method == "POST") {
            $realRoute = explode("/", $this->uri);
            $targetRoute = explode("/", $route);
            if ($route == '/') {
                $targetRoute == [""];
            }

            $parameters = [];

            if (count($realRoute) != count($targetRoute)) {
                return false;
            } else {
                for ($i=0; $i < count($realRoute); $i++) { 
                    $realElement = $realRoute[$i];
                    $targetElement = $targetRoute[$i];

                    if (strStartsWith($targetElement, "[") && strEndsWith($targetElement, "]")) {
                        $parameters[str_replace("[", "", str_replace("]", "", $targetElement))] = $realElement;
                    } else {
                        if ($realElement != $targetElement) {
                            return false;
                        }
                    }
                }
                $this->request->add($parameters);
                $executeable();
                exit();
            }
        }
    }
}