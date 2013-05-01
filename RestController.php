<?php

class RestController {

    function __construct($router, $response, $params, $wire) {
        $this->router = $router;
        $this->wire = $wire;
        $this->response = $response;
        $response->header('X-Powered-By', 'whack/alpha');
        $this->params = $params;
    }

    function dispatch($uri) {
        $uri = parse_url($uri);
        $query = array();
        if (isset($uri['query'])) {
            parse_str($uri['query'], $query);
        }
        $route = $this->router->route($uri['path']);
        if ($route) {
            $this->response->status = 200;
            $this->params->info = $route;
            $this->params->get = $query;
            $action = $route['payload'];
            try {
                if (!is_callable($action) && is_file($action)) {
                    $action = require $action;
                }
                $this->wire->call($action);
            } catch (Exception $e) {
                $this->response->status = 500;
                $this->response->exception = $e;
            }
        } else {
            $this->response->status = 404;
        }
        return $this->response;
    }

}
