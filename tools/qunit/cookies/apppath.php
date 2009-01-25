<?php

define('ALLOW_INCLUSION', 1);
define('DEBUG', 1);
define('APP_PATH', '/custom/'); // Custom APP_PATH
define('ROOT_PATH', '../../../');
require(ROOT_PATH . 'wee/wee.php');

class testCookies extends weeCookies
{
	// Make it public
	public $sCookiePath;
}

try {
	$o = new testCookies;

	$o->sCookiePath == '/custom/' or burn('UnitTestException',
		_WT('Cookie should use APP_PATH as its path when it is custom.'));
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

echo 'success';
