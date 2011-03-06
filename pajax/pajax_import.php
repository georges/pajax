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
	Creates the necessary JavaScript stub for a remote php class
*/

require_once("Pajax.class.php");

header("Content-Type: text/javascript");

$pajax = new Pajax(substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], "/")+1));	
$class = $_SERVER['QUERY_STRING'];

if ($class != "") {
	// Generate class stubs for the remotable classes 
	if ($pajax->loadClass($class)) {
		echo $pajax->getJavaScriptStub($class);
	} else {
		error_log("PAJAX: Can't load '" . $class . "'");
		echo "// Can't load '" . $class . "'";
	}		
} else {
	error_log("PAJAX: Class '" . $class . "' not valid");
	echo "// Class '" . $class . "' not valid! ";
}
?>

