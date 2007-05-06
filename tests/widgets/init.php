<?php

if (isset($_SERVER['argc']))
	return null;

define('ALLOW_INCLUSION',	1);
define('DEBUG',				1);

define('FORM_PATH',	'./');
define('ROOT_PATH',	'../../');
define('TPL_PATH',	'./');

require(ROOT_PATH . 'wee/wee.php');

$Output = weeXHTMLOutput::instance();

return null;

?>
