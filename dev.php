<?php
function strStartsWith($haystack, $needle) {
    $length = strlen($needle);
    return substr($haystack, 0, $length) === $needle;
}

$args = $_SERVER["argv"];

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
        else if (strStartsWith($arg, "--host")) {
            $host = explode("=", $arg)[1];
        }
    }
} else if ($args[1] == "make") {
    $make = true;
    switch ($args[2]) {
        case 'controller':
            $controllername = $args[3];
            $models = [];

            foreach ($args as $arg) {
                if (strStartsWith($arg, "--require")) {
                    $model = explode("=", $arg)[1];
                    if (!is_file(__DIR__."/app/sql/models/$model.php")) {
                        echo "\033[31mERROR: Model '$model' does not exist.\033[0m\n";
                        exit();
                    } else {
                        array_push($models, "require __DIR__.\"/../sql/models/$model.php\";");
                    }
                }
            }

            $modelstring = implode("\n", $models);

            $controller = fopen(__DIR__."/app/controllers/$controllername.php", "w");

            $php = "<?php
$modelstring

class $controllername {
    public static function classfunction(\$parameter) {
        return \$parameter;
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