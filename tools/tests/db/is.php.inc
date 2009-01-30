<?php

try {
	$this->isEqual($sDBMS, $oDb->is(),
		_WT('weeDatabase::is does not return the correct DBMS name.'));
} catch (IllegalStateException $e) {
	$this->fail(_WT('weeDatabase::is should never throw an IllegalStateException.'));
}

$this->isIdentical(true, $oDb->is($sDBMS),
	_WT('weeDatabase::is should return true when given the DBMS name of the database.'));

$this->isIdentical(false, $oDb->is('not_this_DBMS'),
	_WT('weeDatabase::is should return false when given another DBMS name.'));
