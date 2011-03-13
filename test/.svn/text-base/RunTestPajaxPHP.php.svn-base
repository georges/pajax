<?php 
include("testPajax.php");
$title = 'Pajax PHP Unit Test Run';
?>
<html>
  <head>
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="phpunit/stylesheet.css" type="text/css" media="screen" />
  </head>
  <body>
    <h1><?php echo $title; ?></h1>
    <h2>Test Results</h2>
    <?php
		$result = new PrettyTestResult();
		$suite->run($result);
		$result->report();
	?>
  </body>
</html>
