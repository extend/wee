<?php

// Connect

$oDb = new weePgSQLDatabase(array(
	'host'		=> 'localhost',
	'user'		=> 'wee_tests',
	'password'	=> 'wee_tests',
	'dbname'	=> 'wee_tests',
));

// Test weePgSQLDatabase::getLastError

try
{
	// Trigger an error by trying to select from a non-existing table
	// If the exception is not triggered by the error, abort

	$oDb->query('SELECT * FROM getlasterror');
	$this->fail('The table "getlasterror" should not exist.');
}
catch (DatabaseException $e)
{
	$this->isNotNull($oDb->getLastError(),
		'An error has happened while trying to query, but getLastError returns nothing.');
}
