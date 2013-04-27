<?php

class Response {

    public $version = '1.1';
    public $status = 200;
    public $type;
    public $body;
    public $headers;
    public $exception;
    public $debug = false;
    public $headerFunction = 'header';

    protected $redirectUri;
    protected $statusText = array(
        200 => 'OK',
        404 => 'Not Found',
        500 => 'Internal Server Error',
    );

    function __construct($type, $headers = array()) {
        $this->type = $type;
        $this->headers = $headers;
    }

    function header($key, $value = null) {
        if ($value === null) {
            return $this->headers[$key];
        }
        $this->headers[$key] = $value;
    }

    function redirect($uri, $status = 302) {
        $this->redirectUri = $uri;
        $this->status = $status;
    }

    function sendHeaders() {
        if ($this->redirectUri) {
            $this->header('Location', $this->redirectUri);
        }
        $this->header('Content-Type', $this->type);
        $this->sendHeader('HTTP/'.$this->version.' '
            .$this->status.' '.$this->statusText[$this->status]);
        foreach ($this->headers as $key => $value) {
            $this->sendHeader($key.': '.$value);
        }
    }

    function sendHeader($header) {
        $f = $this->headerFunction;
        $f($header, true, $this->status);
    }

    function prepareBody() {
        $body = $this->body;
        switch ($this->type) {
            case 'application/json':
                $options = $this->debug ? JSON_PRETTY_PRINT : 0;
                $body = json_encode($this->body, $options);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->status = 500;
                    $this->exception = new Exception("json encode failed (".json_last_error().")");
                }
                break;
        }
        return $body;
    }

    function send() {
        $body = $this->prepareBody();
        if ($this->exception && $this->status < 500) {
            $this->status = 500;
            $body = null;
        }
        if ($this->exception && $this->debug) {
            $this->type = 'text/plain';
            $body = (string)$this->exception;
        }
        $this->sendHeaders();
        echo $body;
    }

}
