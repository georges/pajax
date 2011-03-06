<?php

require_once("pajax/PajaxRemote.class.php");

class Calculator extends PajaxRemote {
	function Calculator() {
	}
	
	function add($x, $y) {
	   return $x + $y;
	}

	function multiply($x, $y) {
	   return $x * $y;
	}

	function substract($x, $y) {
	   return $x - $y;
	}

	function divide($x, $y) {
		if ($y != 0) {
			return $x / $y;
		} else {
			return null;
		}
	}
}

?>
