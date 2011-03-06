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

define('CLASS_PATH_DELIMITER', ":");

/*
	Class: Pajax
	Main Class for Pajax system
	Create JavaScript stubs and takes care of class loading of php objects
*/
class Pajax {
	/*
		Constructor: Pajax
		
		Parameters:
			uriPath - URI path to the pajax system
			dispatcher - Name of the script used by the http callback
			classPath - Paths of remote class, seperated by ":"
	
	*/
	function Pajax( $uriPath="/pajax/",
					$dispatcher="pajax_call_dispatcher.php", 
					$classPath="..:../test") {
		$this->uriPath = $uriPath;
		$this->dispatcher = $dispatcher;
		$this->classPath = $classPath;
	}

	/*
		Method: loadClass
		Dynamically load a class file
		
		Parameters:
			className - Class to be loaded. It will look for a file that is
			sufixed by ".class.php"
		
		Returns:
			true - Class loaded successfully
			false - Failed to load class
	*/
	function loadClass($className) {
		// Strip path chars from classname
		$className = str_replace(array(".", "/", "\\", ".."), "", $className);
		$paths = split(CLASS_PATH_DELIMITER, $this->classPath);
		foreach ($paths as $path) {
			$classPath = $path . "/" . $className . ".class.php";
			$root = $_SERVER['PATH_TRANSLATED'];
			if (file_exists ($classPath)) {
				require_once($classPath);
				return class_exists($className);
			}
		}
		return false;
	}

	/*
		Method: isRemotable
		Determines if a class can be invoked remotly
		
		Parameters:
			className - Class name to check. It expects the class to be loaded already
		
		Returns:
			true - Class is a subclass of PajaxRemote
			false - Otherwise
	*/
	function isRemotable($className) {
		if (class_exists($className)) {
			eval("\$obj = new $className();");
			return strtolower(get_parent_class($obj)) == strtolower("PajaxRemote");
		} else {
			return false;
		}
	}
	
	/*
		Method: getJavaScriptStub
		Return the JavaScript stub class equivalent for a php class
		
		Parameters:
			className - Class name to build stub for. It expects the class to be loaded already. 

		Returns:
			String contain JavaScript stub for the Class. If the class is not remotable, 
			it will return a string with an error surrounded by html comments tag. 
			
	*/
	function getJavaScriptStub($className) {
		if (! $this->isRemotable($className)) {
			return "<!-- Class '". $className . "' is not remotable -->";
		}
	
		$classMethods = get_class_methods($className);
		ob_start();			
?>
		
function <?php echo $className ?>(listener) {
	this.__pajax_object_id = "<?php echo md5(uniqid(rand(), true))?>" + __pajax_get_next_id();
	this.__pajax_listener = listener;
}

function <?php echo $className ?>Listener() { };
<?php echo $className."Listener.prototype = new PajaxListener();" ?>
				
<?php
		// Create a stub function for each method
		foreach ($classMethods as $methodName) {
		
			// Skip the constructors
			if (strtolower($methodName) != strtolower($className) && strtolower($methodName) != strtolower("PajaxRemote")) {
				/* 
				 In each method, copy the argument because arguments scope is 
				 lost if passed to another function. There seems to always
				 be an argument present even if invoked with none, take that into 
				 account.
				 
				 Create an empty stub for call back if the class is to be used
				 asynchronously. These methods are meant to be overriden in the 
				 client
				*/
?>

<?php echo $className."Listener.prototype.on".ucfirst($methodName)?>=function(result) {};

<?php echo $className.".prototype.".$methodName?> = function() {
	if (arguments.length > 0 && typeof arguments[0] != 'undefined' ) {
		params = new Array();
		for (var i = 0; i < arguments.length; i++) {
			params[i] = arguments[i];
		}
	} else {
		params = null;
	}
	
	return new PajaxConnection("<?php echo $this->uriPath . $this->dispatcher?>").remoteCall(this.__pajax_object_id, "<?php echo $className ?>", "<?php echo $methodName ?>", params, this.__pajax_listener);
}
	
<?php 
			}
		}

		$html = ob_get_contents();
		ob_end_clean();
		return $html;		
	}
}

?>
