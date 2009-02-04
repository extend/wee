<?php

define('ALLOW_INCLUSION', 1);
define('DEBUG', 1);
define('ROOT_PATH', '../../../');
require(ROOT_PATH . 'wee/wee.php');
$iStep = array_value($_GET, 'step', 1);

try {
	$o = new weeCookies;

	if ($iStep == 1) {
		unset($o['testcookie']);
		$o->set('timecookie', 'this is going to expire in a week', 1);
	} else {
		isset($o['testcookie']) and burn('UnitTestException',
			_WT('Cookie "testcookie" should not have been found.'));

		isset($o['timecookie']) and burn('UnitTestException',
			_WT('Cookie "timecookie" should not have been found.'));
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

echo 'success';
