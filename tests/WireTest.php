<?php

require __DIR__.'/bootstrap.php';

class WireTestCase extends PHPUnit_Framework_TestCase {
	
	function testAccessors() {
		$wire = new Wire;
		$wire->foo = function(){
			return 'bar';
		};
		$this->assertEquals('bar', $wire->foo());
		$this->assertTrue(isset($wire->foo));
	}
	
	function testSingleton() {
		$wire = new Wire;
		$obj = new stdClass;
		$fn = function() use($obj) {
			return $obj;
		};
		$wire->Foo = $fn;
		$this->assertSame($obj, $wire->Foo());
		$this->assertNotEquals($fn, $wire->Foo);
	}
	
	function testProvider() {
		$wire = new Wire;
		$fn = function() {
			static $i = 0;
			return (object)array('i' => $i++);
		};
		$wire->newObj = $fn;
		$a = $wire->newObj();
		$b = $wire->newObj();
		$this->assertNotEquals($a, $b);
		$this->assertNotEquals(1, $a);
		$this->assertNotEquals(2, $b);
	}
	
	function testNotSettingClosureThrowsException() {
		$e = null;
		try {
			$wire = new Wire;
			$wire->foo = null;
		} catch (Exception $e){

		}
		$this->assertNotNull($e);
		$this->assertEquals("key(foo) must be a closure", $e->getMessage());
	}
	
}

