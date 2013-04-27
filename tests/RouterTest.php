<?php

require __DIR__.'/bootstrap.php';

class RouterTest extends PHPUnit_Framework_TestCase {

    function setUp() {
        $server = array('HTTP_METHOD' => 'GET');
        $this->router = new Router($server);
    }

    function testMatchesRootRoute() {
        $expectedParams = array(
            'method' => null,
            'payload' => null,
        );
        $this->router->addRoute(null, '/');
        $params = $this->router->route('/');
        $this->assertEquals($expectedParams, $params);
    }

    function testMatchesRouteWithOnlyParam() {
        $expectedParams = array(
            'method' => null,
            'payload' => null,
            'id' => 12,
        );
        $this->router->addRoute(null, '/:id');
        $params = $this->router->route('/12');
        $this->assertEquals($expectedParams, $params);
    }

    function testMatchesSameRouteTwoTimes() {
        $expectedParams = array(
            'method' => null,
            'payload' => null,
            'id' => 12,
        );
        $this->router->addRoute(null, '/:id');
        $params = $this->router->route('/12');
        $this->assertEquals($expectedParams, $params);
        $params = $this->router->route('/12');
        $this->assertEquals($expectedParams, $params);
    }

    function testMatchesRouteWithNoParams() {
        $expectedParams = array(
            'method' => null,
            'payload' => null,
        );
        $this->router->addRoute(null, '/narwal');
        $params = $this->router->route('/narwal');
        $this->assertEquals($expectedParams, $params);
    }

    function testMatchesRouteWithOneParam() {
        $expectedParams = array(
            'method' => null,
            'payload' => null,
            'id' => 12,
        );
        $this->router->addRoute(null, '/narwal/:id');
        $params = $this->router->route('/narwal/12');
        $this->assertEquals($expectedParams, $params);
    }

    function testMatchesRouteWithTwoParams() {
        $expectedParams = array(
            'method' => null,
            'payload' => null,
            'id' => 12,
            'id2' => 33,
        );
        $this->router->addRoute(null, '/narwal/:id/:id2');
        $params = $this->router->route('/narwal/12/33');
        $this->assertEquals($expectedParams, $params);
    }

    function testMatchesRouteWithTwoParamsAndGap() {
        $expectedParams = array(
            'method' => null,
            'payload' => null,
            'id' => 12,
            'id2' => 33,
        );
        $this->router->addRoute(null, '/narwal/:id/gap/:id2');
        $params = $this->router->route('/narwal/12/gap/33');
        $this->assertEquals($expectedParams, $params);
    }

    function testMatchesFirstRoute() {
        $expectedParams = array(
            'method' => null,
            'payload' => null,
            'id' => 12,
        );
        $this->router->addRoute(null, '/narwal/:id');
        $this->router->addRoute(null, '/narwal');
        $params = $this->router->route('/narwal/12');
        $this->assertEquals($expectedParams, $params);
    }

    function testMatchesSecondRoute() {
        $expectedParams = array(
            'method' => null,
            'payload' => null,
            'id' => 12,
        );
        $this->router->addRoute(null, '/narwal');
        $this->router->addRoute(null, '/narwal/:id');
        $params = $this->router->route('/narwal/12');
        $this->assertEquals($expectedParams, $params);
    }

    function testMatchesFirstRouteAndShadowsSecond() {
        $expectedParams = array(
            'method' => null,
            'payload' => null,
            'id' => 12,
        );
        $this->router->addRoute(null, '/narwal/:id');
        $this->router->addRoute(null, '/narwal/:id/:id2');
        $params = $this->router->route('/narwal/12');
        $this->assertEquals($expectedParams, $params);
    }

    function testMatchesNoRoute() {
        $expectedParams = array(
            'method' => null,
            'payload' => null,
            'id' => 12,
        );
        $this->assertNull($this->router->route('/krawappel'));
        $this->router->addRoute(null, '/pony');
        $this->router->addRoute(null, '/unicorn');
        $this->assertNull($this->router->route('/krawappel'));
    }

    function testMatchesRouteAndAddsPayload() {
        $expectedParams = array(
            'method' => null,
            'payload' => 'myPayload',
            'id' => 12,
        );
        $this->router->addRoute(null, '/narwal/:id', 'myPayload');
        $params = $this->router->route('/narwal/12');
        $this->assertEquals($expectedParams, $params);
    }

    function testMatchesRouteByRequestMethod() {
        $expectedParams = array(
            'method' => 'post',
            'payload' => 'postRequest',
            'id' => 12,
        );
        $server = array('HTTP_METHOD' => 'POST');
        $this->router = new Router($server);
        $this->router->addRoute('get', '/narwal/:id', 'getRequest');
        $this->router->addRoute('post', '/narwal/:id', 'postRequest');
        $params = $this->router->route('/narwal/12');
        $this->assertEquals($expectedParams, $params);
    }

}
