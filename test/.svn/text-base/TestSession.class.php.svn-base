<?php

require_once("../pajax/PajaxRemote.class.php");

class TestSession extends PajaxRemote {
	var $counter;
	
	function TestSession() {
		$this->counter=0;
	}
	
	function increment() {
		$this->counter++;
		return $this->counter;
	}
	
	function getCount() {
		return $this->counter;
	}
}	

?>