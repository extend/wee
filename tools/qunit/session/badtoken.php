<?php

define('ALLOW_INCLUSION', 1);
define('DEBUG', 1);
define('ROOT_PATH', '../../../');
require(ROOT_PATH . 'wee/wee.php');
$iStep = array_value($_GET, 'step', 1);

try {
	$o = new weeSession(array('check.token' => true));

	if ($iStep == 1) {
		isset($o['session_token']) or burn('UnitTestException',
			_WT('The session token was not found.'));
	} elseif ($iStep == 2) {
		isset($o['session_token']) or burn('UnitTestException',
			_WT('The session token was not found.'));

		setcookie('session_token', 'wrong token');
	} else {
		isset($o['session_token']) and burn('UnitTestException',
			_WT('The session should have been reinitialized.'));
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

echo 'success';
