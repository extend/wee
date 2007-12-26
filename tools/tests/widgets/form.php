<?php

require('init.php');

if (defined('WEE_CLI'))
	return null;

fire(empty($_GET['type']) || !ctype_alnum($_GET['type']));
echo weeFormTest($_GET['type'])->toString();

?>
