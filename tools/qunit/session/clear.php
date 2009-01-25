<?php

define('ALLOW_INCLUSION', 1);
define('DEBUG', 1);
define('ROOT_PATH', '../../../');
require(ROOT_PATH . 'wee/wee.php');
$iStep = array_value($_GET, 'step', 1);

try {
	$o = new weeSession;

	if ($iStep == 1) {
		$o['cleartest'] = 'not cleared';

		isset($o['cleartest']) or burn('UnitTestException',
			_WT('The session value "cleartest" was not found.'));
	} elseif ($iStep == 2) {
		isset($o['cleartest']) or burn('UnitTestException',
			_WT('The session value "cleartest" was not found.'));

		$o->clear();

		isset($o['cleartest']) and burn('UnitTestException',
			_WT('The session value "cleartest" should not have been found.'));
	} elseif ($iStep == 3) {
		isset($o['cleartest']) and burn('UnitTestException',
			_WT('The session value "cleartest" should not have been found.'));

		$o->clear();
		$o['cleartest'] = 'not cleared';

		isset($o['cleartest']) or burn('UnitTestException',
			_WT('The session value "cleartest" was not found.'));
	} else {
		isset($o['cleartest']) or burn('UnitTestException',
			_WT('The session value "cleartest" was not found.'));
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

echo 'success';
