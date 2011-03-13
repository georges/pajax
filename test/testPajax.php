<?php
require "phpunit/phpunit.php";
require "../pajax/Pajax.class.php";

class PajaxTest extends TestCase {
	function PajaxTest($name = "PajaxTest") {
		$this->TestCase($name);
	}
	
	function setUp() {
		$this->pajax = new Pajax();
	}
	
	function testSuccess() {
		$this->assert(true, "ok");
	}
	
	function testFailure() {
		$this->assert(false, "not ok");
	}
}

$suite = new TestSuite("PajaxTest");
?>
