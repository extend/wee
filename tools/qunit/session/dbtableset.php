<?php

define('ALLOW_INCLUSION', 1);
define('DEBUG', 1);
define('ROOT_PATH', '../../../');
require(ROOT_PATH . 'wee/wee.php');
require('dbtableinit.php.inc');
$iStep = array_value($_GET, 'step', 1);

try {
	$o = new weeSessionDbTable(array('db' => $oDb, 'table' => 'sessions'));
	$o->setDb($oDb);

	$aArray = array(1, 2, 3, 'soleil');

	if ($iStep == 1) {
		$oDb->query('
			CREATE TABLE IF NOT EXISTS `sessions` (
				`session_id` CHAR(32)  NOT NULL,
				`session_path` CHAR(64)  NOT NULL,
				`session_name` CHAR(32)  NOT NULL,
				`session_time` INTEGER UNSIGNED NOT NULL,
				`session_data` TEXT ,
				PRIMARY KEY (`session_id`, `session_path`, `session_name`)
			)
			ENGINE = MyISAM;
		');
	} elseif ($iStep == 2) {
		$o['test'] = 42;
		$o['array'] = $aArray;

		isset($o['test']) or burn('UnitTestException',
			_WT('The session value "test" was not found.'));

		isset($o['array']) or burn('UnitTestException',
			_WT('The session value "array" was not found.'));
	} elseif ($iStep == 3) {
		isset($o['test']) or burn('UnitTestException',
			_WT('The session value "test" was not found.'));

		isset($o['array']) or burn('UnitTestException',
			_WT('The session value "array" was not found.'));

		$o['test'] == 42 or burn('UnitTestException',
			_WT('The session value "test" is incorrect.'));

		$o['array'] == $aArray or burn('UnitTestException',
			_WT('The session value "array" is incorrect.'));

		// Prepare for next step
		$o['test'] = 'answer';
		unset($o['array']);

		$o['test'] == 'answer' or burn('UnitTestException',
			_WT('The session value "test" is incorrect.'));

		isset($o['array']) and burn('UnitTestException',
			_WT('The session value "array" should not exist.'));
	} else {
		$o['test'] == 'answer' or burn('UnitTestException',
			_WT('The session value "test" is incorrect.'));

		isset($o['array']) and burn('UnitTestException',
			_WT('The session value "array" should not exist.'));

		$oDb->query('DROP TABLE `sessions`');
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

echo 'success';
