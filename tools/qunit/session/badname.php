<?php

define('ALLOW_INCLUSION', 1);
define('DEBUG', 1);
define('ROOT_PATH', '../../../');
require(ROOT_PATH . 'wee/wee.php');
$iStep = array_value($_GET, 'step', 1);

try {
	$o = new weeSession;

	if ($iStep == 1) {
		$o['test'] = 'ok';

		isset($o['test']) && $o['test'] == 'ok' or burn('UnitTestException',
			_WT('The session value was not found.'));
	} elseif ($iStep == 2) {
		isset($o['test']) && $o['test'] == 'ok' or burn('UnitTestException',
			_WT('The session value was not found.'));

		setcookie(session_name(), '{garbage}');
	} else {
		isset($o['test']) and burn('UnitTestException',
			_WT('The session should have been reinitialized.'));
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

echo 'ok';
