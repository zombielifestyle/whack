<?php

require __DIR__.'/bootstrap.php';

class ParamsTest extends PHPUnit_Framework_TestCase {

    function testReadValue() {
        $sections = array(
            'get' => array('narwal' => 'bacon'),
            'post' => array(),
        );
        $params = new Params($sections);
        $this->assertEquals('bacon', $params->get('narwal'));
        $this->assertEquals(null, $params->post('narwal'));
    }

    function testDefaultValue() {
        $sections = array(
            'get' => array(),
        );
        $params = new Params($sections);
        $this->assertEquals('sashimi', $params->get('narwal', 'sashimi'));
    }

    function testCheckExistance() {
        $sections = array(
            'get' => array('narwal' => 'bacon'),
            'post' => array(),
        );
        $params = new Params($sections);
        $this->assertEquals(true, $params->hasGet('narwal'));
        $this->assertEquals(false, $params->hasPost('narwal'));
    }

    function testThrowExceptionForUnknownSections() {
        $params = new Params(array());
        try {
            $params->get('void');
            $this->fail("expected exception");
        } catch (Exception $e) {
            
        }
    }

}
