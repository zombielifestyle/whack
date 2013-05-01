<?php

class TaskController {

    function __construct($path, $params, $wire) {
        $this->path = $path;
        $this->wire = $wire;
        $this->params = $params;
    }

    function dispatch($argv) {
        $task = null;
        $file = null;
        if (isset($argv[1])) {
            $task = $argv[1];
            $file = $this->path.'/'.$task.'.php';
        }
        if (!$task || !is_file($file)) {
            throw new Exception("first argument must be a task located in path($this->path)");
        }

        $args = array_splice($argv, 2);
        $mappedArgs = array();
        foreach ($args as $arg) {
            $pair = explode('=', $arg);
            if (count($pair) !== 2) {
                throw new Exception("additional arguments must be 'key=value' pairs");
            }
            $mappedArgs[$pair[0]] = $pair[1];
        }
        $this->params->info = $mappedArgs;

        $action = require $file;
        return $this->wire->call($action);
    }

}
