<?php
class Cookies {
    public function __construct($cookies) {
        foreach ($cookies as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function create($name, $value, $duration = 2592000 /*30 Days in seconds*/) {
        setcookie($name, $value, time() + $duration, '/');
        $this->{$name} = $value;
    }
}