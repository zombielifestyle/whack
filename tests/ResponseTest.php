<?php

require __DIR__.'/bootstrap.php';

class ResponseTest extends PHPUnit_Framework_TestCase {

    function setUp() {
        $this->headers = '';
        $this->response = new Response('text/plain');
        $this->response->headerFunction = function($header) {
            $this->headers.= $header."\r\n";
        };    
    }

    function testType() {
        $this->assertEquals('text/plain', $this->response->type);
    }

    function testHeader() {
        $this->response->header('Accept', 'text/plain');
        $this->assertEquals('text/plain', $this->response->header('Accept'));
    }

    function testSendUnmodifiedHeaders() {
        $this->response->sendHeaders();

        $expectedHeaders = "HTTP/1.1 200\r\n";
        $expectedHeaders.= "Content-Type: text/plain\r\n";
        
        $this->assertEquals($expectedHeaders, $this->headers);
    }

    function testSendCustomHeaders() {
        $this->response->header('X-Custom', 'narwal');
        $this->response->sendHeaders();

        $expectedHeaders = "HTTP/1.1 200\r\n";
        $expectedHeaders.= "X-Custom: narwal\r\n";
        $expectedHeaders.= "Content-Type: text/plain\r\n";
        
        $this->assertEquals($expectedHeaders, $this->headers);
    }

    function testSendRedirectHeaders() {
        $this->response->redirect('http://ponyhof.de');
        $this->response->sendHeaders();

        $expectedHeaders = "HTTP/1.1 302\r\n";
        $expectedHeaders.= "Location: http://ponyhof.de\r\n";
        $expectedHeaders.= "Content-Type: text/plain\r\n";
        
        $this->assertEquals($expectedHeaders, $this->headers);
    }

    function testSend() {
        $this->response->body = 'narwal';
        ob_start();
        $this->response->send();
        $body = ob_get_clean();

        $expectedHeaders = "HTTP/1.1 200\r\n";
        $expectedHeaders.= "Content-Type: text/plain\r\n";
        
        $this->assertEquals($expectedHeaders, $this->headers);
        $this->assertEquals('narwal', $body);
    }

    function testSendJson() {
        $this->response->type = 'application/json';
        $this->response->body = 'narwal';
        ob_start();
        $this->response->send();
        $body = ob_get_clean();

        $expectedHeaders = "HTTP/1.1 200\r\n";
        $expectedHeaders.= "Content-Type: application/json\r\n";
        
        $this->assertEquals($expectedHeaders, $this->headers);
        $this->assertEquals('"narwal"', $body);
    }

    function testSendError() {
        $this->response->body = 'narwal';
        $this->response->exception = 'error';
        ob_start();
        $this->response->send();
        $body = ob_get_clean();

        $expectedHeaders = "HTTP/1.1 500\r\n";
        $expectedHeaders.= "Content-Type: text/plain\r\n";
        
        $this->assertEquals($expectedHeaders, $this->headers);
        $this->assertEquals(null, $body);
    }

    function testSendErrorDebug() {
        $this->response->debug = true;
        $this->response->body = 'narwal';
        $this->response->exception = 'error';
        ob_start();
        $this->response->send();
        $body = ob_get_clean();

        $expectedHeaders = "HTTP/1.1 500\r\n";
        $expectedHeaders.= "Content-Type: text/plain\r\n";
        
        $this->assertEquals($expectedHeaders, $this->headers);
        $this->assertEquals('error', $body);
    }

}
