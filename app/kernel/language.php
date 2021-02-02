<?php
class Language {
    public function __construct($locale = "en") {
        $this->locale = $locale;
    }

    public function setLocale($locale) {
        $this->locale = $locale;
    }

    public function getLocale() {
        return $this->locale;
    }

    public function get($identifier) {
        $elements = explode(".", $identifier);
        $file = __DIR__."/../lang/".$this->locale."/".$elements[0].".php";
        if (is_file($file)) {
            include($file);
            return $lang[$elements[1]];
        }
        return $identifier;
    }

}