<?php

class Router {

    protected $routes = array();

    function __construct($server) {
        $this->method = $server;
        if (isset($server['HTTP_METHOD'])) {
            $this->method = strtolower($server['HTTP_METHOD']);
        }
    }

    function addRoute($method, $path, $controller = null) {
        $route = array(strtolower($method), strtolower($path), $controller);
        $this->routes[] = $route;
    }

    function route($url) {
        $urlParts = $this->splitPath(parse_url($url, PHP_URL_PATH));
        foreach ($this->routes as $route) {
            if ($route[0] && $this->method !== $route[0]) {
                continue;
            }
            $parts = $this->splitPath($route[1]);
            if (count($urlParts) === count($parts)) {
                $params = array();
                $matched = true;
                foreach ($urlParts as $key => $part) {
                    if (strpos($parts[$key], ':') === 0) {
                        $params[trim($parts[$key], ':')] = $part;
                        continue;
                    }
                    if ($part !== $parts[$key]) {
                        $matched = false;
                        break;
                    }
                }
                if ($matched) {
                    $params['method'] = $route[0];
                    $params['payload'] = $route[2];
                    return $params;
                }
            }
        }
        return null;
    }

    function splitPath($path) {
        return explode('/', trim($path, '/'));
    }

}