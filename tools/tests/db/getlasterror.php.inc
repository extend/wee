<?php

$this->isFalse(empty($oDb), sprintf(_WT('The file %s requires a database object $oDb in the current scope before it is included.'), __FILE__));

try {
    $oDb->getLastError();
    $this->fail(_WT('weeDatabase::getLastError should throw an IllegalStateException when no error occured.'));
} catch (IllegalStateException $e) {}

try {
	$oDb->query('INVALID_SQL');
	$this->fail(_WT('weeDatabase::query should have thrown a DatabaseException.'));
} catch (DatabaseException $e) {
    try {
        $this->isNotEqual('', $oDb->getLastError(),
            _WT('weeDatabase::getLastError should not be empty after an erroneous weeDatabase::query call.'));
    } catch (IllegalStateException $e) {
        $this->fail(_WT('weeDatabase::getLastError should not throw an IllegalStateException after an erroneous weeDatabase::query call.'));
    }
}

$oDb->query('SELECT 1');

try {
    $oDb->getLastError();
    $this->fail(_WT('weeDatabase::getLastError should throw an IllegalStateException again after a correct weeDatabase::query call.'));
} catch (IllegalStateException $e) {}
