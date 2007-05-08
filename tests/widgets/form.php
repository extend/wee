<?php

if (isset($_SERVER['argc']))
	return null;

require('init.php');

fire(empty($_GET['type']) || !ctype_alnum($_GET['type']));
echo weeFormTest($_GET['type']);

?>
