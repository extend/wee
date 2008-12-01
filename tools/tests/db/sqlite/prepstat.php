<?php

require(dirname(__FILE__) . '/connect.php.inc');

try {
	$oInsertStat = $oDb->prepare('
		INSERT INTO prepstat (ps_integer, ps_varchar, ps_timestamp, ps_boolean)
		VALUES (:ps_integer, :ps_varchar, :ps_timestamp, :ps_boolean)
	');
	$this->fail('The sqlite database driver cannot use prepared statements.');
} catch (Exception $e) {
	$this->isInstanceOf($e, 'BadMethodCallException',
		'The exception thrown by weeSQLiteDatabase::prepare should be BadMethodCallException.');
}
