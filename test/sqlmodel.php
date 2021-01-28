<?php
require __DIR__."/../app/sql/dependencies/model.php";
class TestModel extends Model {
    private $tablename = "test";

    function __construct() {
        parent::__construct($this->tablename);
    }
}

$sql = new TestModel();

//DELETE
$sql->delete();
$sql->where("geschlecht", "=", "Pflanze");
$sql->execute();

//INSERT
$sql->insert(["name", "age", "geschlecht"], ["Bambus", 0, "Pflanze"]);
$sql->execute();

//SELECT
$sql->get("*");
$sql->where("age", ">", "18");
$result = $sql->execute();
var_dump($sql->fetch("all", $result));

$sql->get("*");
$sql->where("geschlecht", "=", "Weiblich");
$sql->limit(1);
$result = $sql->execute();
var_dump($sql->fetch("all", $result));

$sql->get("*");
$sql->orderBy("name", "ASC");
$result = $sql->execute();
var_dump($sql->fetch("all", $result));

//UPDATE
$sql->update(["age"], [1]);
$sql->where("geschlecht", "=", "Pflanze");
$sql->execute();