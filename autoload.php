<?php

spl_autoload_register(function($className) {
    $path = __DIR__.'/'.$className.'.php';
    if (is_file($path)) {
        require $path;
    }
}, true);

