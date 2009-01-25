<?php

define('ALLOW_INCLUSION', 1);
define('DEBUG', 1);
define('ROOT_PATH', '../../../');
require(ROOT_PATH . 'wee/wee.php');
$iStep = array_value($_GET, 'step', 1);

try {
	$o = new weeCookies;

	if ($iStep == 1) {
		$o->set('testcookie', 'this is a test');
		$o->set('timecookie', 'this is going to expire in a week', time() + 3600 * 24 * 7);
	} elseif ($iStep == 2) {
		isset($o['testcookie']) or burn('UnitTestException',
			_WT('Cookie "testcookie" was not found.'));

		isset($o['timecookie']) or burn('UnitTestException',
			_WT('Cookie "timecookie" was not found.'));

		$o['testcookie'] == 'this is a test' or burn('UnitTestException',
			_WT('Cookie "testcookie" has a wrong value.'));

		$o['timecookie'] == 'this is going to expire in a week' or burn('UnitTestException',
			_WT('Cookie "timecookie" has a wrong value.'));

		// Prepare for next step
		$o['testcookie'] = 'this is a value change test';
	} else {
		$o['testcookie'] == 'this is a value change test' or burn('UnitTestException',
			_WT('Cookie "testcookie" should have had its value changed.'));
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

echo 'success';
