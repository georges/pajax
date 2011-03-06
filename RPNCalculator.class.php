<?php

require_once("pajax/PajaxRemote.class.php");

define('TOO_FEW_ARGUMENTS', "Error: Too few arguments");

class RPNCalculator extends PajaxRemote {
	function RPNCalculator() {
		// Create our stack
		$this->stack = array();
	}
	
	function add() {
		if (count($this->stack) > 1) {
			$result = $this->pop() + $this->pop();
			$this->push($result);
		} else {
			return TOO_FEW_ARGUMENTS;
		}
	}

	function substract() {
		if (count($this->stack) > 1) {
			$a = $this->pop();
			$b = $this->pop();
			$result = $b - $a;
			$this->push($result);
		} else {
			return TOO_FEW_ARGUMENTS;
		}
	}

	function multiply() {
		if (count($this->stack) > 1) {
			$a = $this->pop();
			$b = $this->pop();
			$result = $b * $a;
			$this->push($result);
		} else {
			return TOO_FEW_ARGUMENTS;
		}
	}

	function divide() {
		if (count($this->stack) > 1) {
			$denominator = $this->pop();
			if ($denominator != 0) {
				$numerator = $this->pop();
				$this->push($numerator / $denominator);
			} else {
				$this->push($denominator);
				return "Error: Infinite result";
			}
		} else {
			return TOO_FEW_ARGUMENTS;
		}
	}

	function swap() {
		if (count($this->stack) > 1) {
			$val1 = array_pop($this->stack);
			$val2 = array_pop($this->stack);
			array_push($this->stack	, $val1);
			array_push($this->stack	, $val2);
		} else {
			return TOO_FEW_ARGUMENTS;
		}
	}

	function dup() {
		if (count($this->stack) > 0) {
			$val = array_pop($this->stack);
			array_push($this->stack	, $val);
			array_push($this->stack	, $val);
		} else {
			return TOO_FEW_ARGUMENTS;
		}

	}

	function push($x) {
		if (is_numeric($x)) {
			array_push($this->stack	, $x);
		}
	}

	function pop() {
		$val = array_pop($this->stack);
		if ($val != null) {
			return $val;
		} else {
			return TOO_FEW_ARGUMENTS;
		}
	}
	
	function drop() {
		$this->pop();
	}

	function getStack($depth) {
		$offset = count($this->stack)-$depth;
		if ($offset < 0) {
			$offset = 0;
		}
		return array_slice($this->stack, $offset, $depth);
	}
}

?>
