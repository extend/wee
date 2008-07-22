<?php

if (defined('ALLOW_INCLUSION'))
	return false;

require('init.php.inc');

fire(empty($_GET['type']) || !ctype_alnum($_GET['type']));
echo weeFormTest($_GET['type'])->toString();

?>
