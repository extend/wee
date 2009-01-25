<?php

define('ALLOW_INCLUSION', 1);
define('DEBUG', 1);
define('ROOT_PATH', '../../../');
require(ROOT_PATH . 'wee/wee.php');

class testCookies extends weeCookies
{
	// Make it public
	public $sCookiePath;
}

try {
	// Default path

	$o = new testCookies;

	$sCheck = dirname($_SERVER['REQUEST_URI']) . '/';
	$o->sCookiePath == $sCheck or burn('UnitTestException',
		sprintf(_WT('Cookie path "%s" should have been "%s".'), $o->sCookiePath, $sCheck));

	// Custom path in constructor parameter

	$o = new testCookies(array('path' => '/custom/'));

	$o->sCookiePath == '/custom/' or burn('UnitTestException',
		_WT('Cookie should use the path parameter when it is given.'));
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

echo 'success';
