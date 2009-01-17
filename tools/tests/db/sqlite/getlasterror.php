<?php

require(dirname(__FILE__) . '/connect.php.inc');

// weeSQLiteDatabase::getLastError

try {
	$oDb->getLastError();
	$this->fail(_WT('weeSQLiteDatabase::getLastError should throw an IllegalStateException when no error occured.'));
} catch (IllegalStateException $e) {}

try {
	$oDb->query('INVALID_SQL');
} catch (DatabaseException $e) {
	try {
		$this->isNotNull($oDb->getLastError(),
			_WT('weeSQLiteDatabase::getLastError should return the message of the last error.'));
	} catch (IllegalStateException $e) {
		$this->fail(_WT('weeSQLiteDatabase::getLastError should not throw an IllegalStateException when an error occured.'));
	}
}
