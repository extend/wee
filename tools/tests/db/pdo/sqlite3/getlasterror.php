<?php

require('connect.php.inc');

// Test weePDODatabase::getLastError

$oDb->query('SELECT 1');

try {
	$oDb->getLastError();
	$this->fail(_WT('weePDODatabase::getLastError should throw an IllegalStateException when no error occurred during the execution of the last query.'));
} catch (IllegalStateException $e) {}

try {
	$oDb->query('BLAH');
} catch (DatabaseException $e) {
	try {
		$this->isNotNull($oDb->getLastError(),
			_WT('An error has happened while trying to query, but getLastError returns nothing.'));
	} catch (IllegalStateException $e) {
		$this->fail(_WT('weePDODatabase::getLastError should not throw an IllegalStateException when an error occurred during the execution of the last query.'));
	}
}
