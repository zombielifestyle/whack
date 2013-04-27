<?php

class RestController {

    function __construct($router, $response, $params, $wire) {
        $this->router = $router;
        $this->wire = $wire;
        $this->response = $response;
        $this->params = $params;
    }

    function dispatch($path) {
        $route = $this->router->route($path);
        if ($route) {
            $this->response->status = 200;
            $this->params->info = $route;
            $action = $route['payload'];
            try {
                if (!is_callable($action) && is_file($action)) {
                    $action = require $action;
                }
                call_user_func_array($action, $this->getClosureParameters($action));
            } catch (Exception $e) {
                $this->response->status = 500;
                $this->response->exception = $e;
            }
        } else {
            $this->response->status = 404;
        }
        return $this->response;
    }

    protected function getClosureParameters($closure) {
        $ref = new ReflectionFunction($closure);
        $refParams = $ref->getParameters();
        $params = array();
        foreach ($refParams as $param) {
            $paramName = $param->getName();
            $params[] = $this->wire->$paramName;
        }
        return $params;
    }

}
