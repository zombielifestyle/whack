<?php

require __DIR__.'/bootstrap.php';

class RestControllerTest extends PHPUnit_Framework_TestCase {

    function createRestControllerWithRoute($path, $payload) {
        $wire = new Wire();
        $wire->Narwal = function() { return 'narwal';};
        $wire->Params = function() { return new Params(array());};
        $wire->Response = function() { return new Response('text/plain'); };
        $wire->Router = function() use($path, $payload) {
            $router = new Router(array('HTTP_METHOD' => 'POST'));
            $router->addRoute(null, $path, $payload);
            return $router;
        };
        $wire->RestController = function() {
            return new RestController($this->Router, $this->Response, $this->Params, $this);
        };
        $this->wire = $wire;
        return $wire->RestController;
    }

    function testWiredClosureDispatch() {
        $controller = $this->createRestControllerWithRoute('/:friend', function($Response, $Params, $Narwal) {
            $Response->body = "$Narwal meets " . $Params->info('friend');
        });
        $Response = $controller->dispatch('/pony');
        $this->assertEquals("narwal meets pony", $Response->body);
    }

    function testStatus404() {
        $controller = $this->createRestControllerWithRoute('/narwal', function($Response) {
            $Response->body = 'called';
        });
        $Response = $controller->dispatch('/pony');
        $this->assertNotEquals('called', $Response->body);
        $this->assertEquals(404, $Response->status);
    }

    function testStatus500() {
        $controller = $this->createRestControllerWithRoute('/', function($Response) {
            throw new Exception("bacon");
        });
        $Response = $controller->dispatch('/');
        $this->assertEquals('bacon', $Response->exception->getMessage());
        $this->assertEquals(500, $Response->status);
    }

}
