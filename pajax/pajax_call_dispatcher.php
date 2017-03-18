<?php
/*
	Pajax - Remote (a)synchronous PHP objects in JavaScript.

	(c) Copyright 2005-2011 by Georges Auberger

	Permission is hereby granted, free of charge, to any person
	obtaining a copy of this software and associated documentation
	files (the "Software"), to deal in the Software without
	restriction, including without limitation the rights to use,
	copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following
	conditions:

	The above copyright notice and this permission notice shall be
	included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
	OTHER DEALINGS IN THE SOFTWARE.
*/

/*
	This script will dispatch the request to the proper PHP object and return
	the result of the call. It takes care of marshaling parameters back and
	forth.
*/

if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
  function session_is_registered($x) {return isset($_SESSION[$x]);}
}

require_once('Pajax.class.php');

$pajax = new Pajax();

$input = $HTTP_RAW_POST_DATA;

error_log("PAJAX: Input JSON: " . $input);

$invoke = json_decode($input);

// Marshaled parameters
$class = $invoke->className;
$method = $invoke->method;
$id = $invoke->id;
$output = "null";
$obj = null;

error_log("PAJAX: Dispatching");
// Attempts to load class definition
if ($pajax->loadClass($class)) {	
	/* 
		The session stuff needs to be here, once the class definition has been
		loaded, otherwise the object gets deserialized from the session as 
		__PHP_Incomplete_Class type.
	*/
	session_start();
	if (!session_is_registered('objects')) {
		$_SESSION['objects'] = array();
	} 

	// Get our objects out of the session
	$objects = $_SESSION['objects'];

	// Look if the object exists in the session
	if (isset($objects[$id])) {
		error_log("PAJAX: Restoring object from session");
		$obj = $objects[$id];
	} else {
		if ($pajax->isRemotable($class)) {
			error_log("PAJAX: Creating new object from class '" . $class . "'");	
			eval("\$obj = new $class();");
		} else {
			error_log("PAJAX: Class " . $class . " not remotable");
			$obj = null;
		}
	}
} else {
	error_log("PAJAX: Can't load '" . $class . "'");
}		

if (! is_null($obj) && is_object($obj)) {
	if ($invoke->params == null) {
		$invoke->params = array();
	}
	
	// Invoking the method with parameters
	$ret = call_user_func_array(array(&$obj, $method), $invoke->params);
		
	$output = json_encode($ret);

	error_log("PAJAX: Output JSON: " . $output );
	
	$objects[$id] = $obj;
	$_SESSION['objects'] = $objects;
} else {
	error_log("PAJAX: Could not dispatch to valid object");
}

header("Content-type: text/json");
print($output);

?>
