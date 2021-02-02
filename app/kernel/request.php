<?php
class Request {
    function __construct($args) {
        foreach ($args as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function add($args) {
        foreach ($args as $key => $value) {
            $this->{$key} = $value;
        }
    }
}