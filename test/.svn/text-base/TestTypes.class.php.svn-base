<?php

require_once("../pajax/PajaxRemote.class.php");

class TestObject {
}

class TestTypes extends PajaxRemote {
	
	function getInteger() {
		return 1;
	}

	function getFloat() {
		return 1/3;
	}

	function getString() {
		return "Hello world";
	}

	function getBadString() {
		return "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">";
	}

	function getArrayInteger() {
		return array(1, 2, 3, 4);
	}

	function getArrayString() {
		return array("one", "two", "three", "four");
	}

	function getNull() {
		return NULL;
	}

	function getObject() {
		return new TestObject();
	}

	function getTrue() {
		return true;
	}

	function getFalse() {
		return false;
	}
	
	function isInteger($param) {
		return is_int($param);
	}

	function isFloat($param) {
		return is_float($param);
	}

	function isString($param) {
		return is_string($param);
	}

	function isBoolean($param) {
		return is_bool($param);
	}

	function isArray($param) {
		return is_array($param);
	}

	function isObject($param) {
		return is_object($param);
	}

	function isNull($param) {
		return is_null($param);
	}

	function getParams() {
		return func_get_args();
	}	

	function getParam($param) {
		return $param;
	}	

}

?>