<?php
$args = $_SERVER["argv"];
if (count($args) <= 1) {
    exit();
}

function strStartsWith($haystack, $needle) {
    $length = strlen($needle);
    return substr($haystack, 0, $length) === $needle;
}

$port = "8000";
$host = "localhost";
$server = false;
$make = false;

if ($args[1] == "server") {
    $server = true;

    foreach ($args as $arg) {
        if (strStartsWith($arg, "--port")) {
            $port = explode("=", $arg)[1];
        }
        elseif (strStartsWith($arg, "--host")) {
            $host = explode("=", $arg)[1];
        }
    }
} elseif ($args[1] == "make") {
    $make = true;
    switch ($args[2]) {
        case 'controller':
            $controllername = $args[3];
            $models = [];

            $route = "";
            $routeType = "get";

            foreach ($args as $arg) {
                if (strStartsWith($arg, "--require")) {
                    $model = explode("=", $arg)[1];
                    if (!is_file(__DIR__."/app/sql/models/$model.php")) {
                        echo "\033[31mERROR: Model '$model' does not exist.\033[0m\n";
                        exit();
                    } else {
                        array_push($models, "require __DIR__.\"/../sql/models/$model.php\";");
                    }
                } elseif (strStartsWith($arg, "--addRoute")) {
                    $route = explode("=", $arg)[1];
                } elseif (strStartsWith($arg, "--routeType")) {
                    $tempRouteType = strtolower(explode("=", $arg[1]));
                    if ($tempRouteType == "get" || $tempRouteType == "post") {
                        $routeType = $tempRouteType;
                    }
                }
            }

            if ($route != "") {
                $routes = file_get_contents(__DIR__."/route/controllers.php");
                $routes .= "\n\$ROUTE->$routeType(\"$route\", function () {process(\"$controllername::classfunction\");});";
                file_put_contents(__DIR__."/route/controllers.php", $routes);
            }

            $modelstring = implode("\n", $models);

            $controller = fopen(__DIR__."/app/controllers/$controllername.php", "w");

            $php = "<?php
$modelstring

class $controllername {
    public static function classfunction(\$Request) {
        \$result = \"Hello World\";
        echo \$result;
    }
}
";
            fwrite($controller, $php);
            fclose($controller);
            echo "\033[32mSuccessfully created controller $controllername at ".__DIR__."/app/controllers/$controllername.php\033[0m\n\n";

            break;

        case 'model':
            $modelname = $args[3];
            $classname = (strtolower($modelname) != "model") ? $modelname : "_$modelname" ;

            $model = fopen(__DIR__."/app/sql/models/$modelname.php", "w");

            $php = "<?php
    require __DIR__.\"/../dependencies/model.php\";

    class $classname extends Model {
        private \$tablename = \"$modelname\";
        function __construct() {
            parent::__construct(\$this->tablename);
        }
    }
            ";
            fwrite($model, $php);
            fclose($model);
            echo "\033[32mSuccessfully created model $modelname at ".__DIR__."/app/sql/models/$modelname.php\033[0m\n\n";
            break;
        
        default:
            echo "\033[31mERROR: Undefined '$args[2]'\033[0m\n";
            break;
    }
}

if ($server) {
    echo "\033[31m>>> \033[33mStarting Developmentserver \033[31m<<<\033[0m\n";
    exec("php -S $host:$port public/index.php");
}