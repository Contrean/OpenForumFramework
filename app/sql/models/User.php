<?php
    require_once __DIR__."/../dependencies/model.php";

    class User extends Model {
        private $tablename = "user";
        function __construct() {
            parent::__construct($this->tablename);
        }
    }