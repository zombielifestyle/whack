<?php

class Wire {

	private $refs = array();
	
	function __call($method, $args){
		if (isset($this->refs[$method])) {
			$f = $this->refs[$method];
			return call_user_func_array($f, $args);
		}
		throw new Exception("$method wire key not defined");
	}
	
	function __set($key, $value){
		if (!$value instanceof Closure) {
			throw new Exception("key($key) must be a closure");
		}
		$value = $value->bindTo($this, $this);
		if ($key{0} != strtolower($key{0})) {
			$value = function () use($value) {
				static $cache = null;
				if (!$cache) {
					$cache = $value();
				}
				return $cache;
			};
		}
		$this->refs[$key] = $value;
	}
	
	function __get($key){
		return $this->__call($key, array());
	}
	
	function __isset($key){
		return isset($this->refs[$key]);
	}

	function call($closure) {
        $ref = new ReflectionFunction($closure);
        $refParams = $ref->getParameters();
        $params = array();
        foreach ($refParams as $param) {
            $paramName = $param->getName();
            $params[] = $this->$paramName;
        }     
        return call_user_func_array($closure, $params);
	}
	
}
