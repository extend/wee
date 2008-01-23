<?php

// Connect

$oDb = new weeMySQLDatabase(array(
	'host'		=> 'localhost',
	'user'		=> 'wee_tests',
	'password'	=> 'wee_tests',
	'dbname'	=> 'wee_tests',
));

try
{
	$oInsertStat = $oDb->prepare('
		INSERT INTO prepstat (ps_integer, ps_varchar, ps_timestamp, ps_boolean)
		VALUES (:ps_integer, :ps_varchar, :ps_timestamp, :ps_boolean)
	');
	$this->fail('The mysql database driver cannot use prepared statements.');
}
catch (Exception $e)
{
	$this->isInstanceOf($e, 'BadMethodCallException',
		'The exception thrown by weeMySQLDatabase::prepare should be BadMethodCallException.');
}

unset($oDb);
