<?php

class Params {

    protected $sections = array();

    function __construct($sections) {
        $this->sections = $sections;
    }

    function __call($sectionIndex, $args) {
        $method = 'pick';
        if (strpos($sectionIndex, 'has') === 0) {
            $sectionIndex = strtolower(substr($sectionIndex, 3));
            $method = 'has';
        }
        if (!array_key_exists($sectionIndex, $this->sections)) {
            throw new Exception("sectionIndex($sectionIndex) not defined");
        }
        array_unshift($args, $this->sections[$sectionIndex]);
        return call_user_func_array(array($this, $method), $args);      
    }

    function __set($key, $value) {
        $this->sections[$key] = $value;
    }

    protected function has($hash, $key) {
        return array_key_exists($key, $hash);
    }

    protected function pick($hash, $key, $default = null) {
        if (array_key_exists($key, $hash)) {
            return $hash[$key];
        }
        return $default;
    }

}
