<?php

define('ALLOW_INCLUSION', 1);
define('DEBUG', 1);
define('ROOT_PATH', '../../../');
require(ROOT_PATH . 'wee/wee.php');
require('dbtableinit.php.inc');
$iStep = array_value($_GET, 'step', 1);

try {
	$o = new weeSessionDbTable(array('db' => $oDb, 'table' => 'sessions'));

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

		$o['cleartest'] = 'not cleared';

		isset($o['cleartest']) or burn('UnitTestException',
			_WT('The session value "cleartest" was not found.'));
	} elseif ($iStep == 3) {
		isset($o['cleartest']) or burn('UnitTestException',
			_WT('The session value "cleartest" was not found.'));

		$o->clear();

		isset($o['cleartest']) and burn('UnitTestException',
			_WT('The session value "cleartest" should not have been found.'));
	} elseif ($iStep == 4) {
		isset($o['cleartest']) and burn('UnitTestException',
			_WT('The session value "cleartest" should not have been found.'));

		$o->clear();
		$o['cleartest'] = 'not cleared';

		isset($o['cleartest']) or burn('UnitTestException',
			_WT('The session value "cleartest" was not found.'));
	} else {
		isset($o['cleartest']) or burn('UnitTestException',
			_WT('The session value "cleartest" was not found.'));

		$oDb->query('DROP TABLE `sessions`');
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

echo 'success';
