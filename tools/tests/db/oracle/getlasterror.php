<?php

require('connect.php.inc');

// Test weeOracleDatabase::getLastError

try
{
	// Trigger an error by trying to select from a non-existing table
	// If the exception is not triggered by the error, abort

	$oDb->query('SELECT * FROM getlasterror');
	$this->fail(_WT('The table "getlasterror" should not exist.'));
}
catch (DatabaseException $e)
{
	$this->isNotNull($oDb->getLastError(),
		_WT('An error has happened while trying to query, but getLastError returns nothing.'));
}
