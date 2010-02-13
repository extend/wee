<?php

if ($mPK !== null)
	try {
		$oDb->getPKId('NOT_FOUND');
		$this->fail(_WT('weeDatabase::getPKId should throw a DatabaseException when the given sequence does not exist in the database.'));
	} catch (DatabaseException $e) {}

try {
	$oDb->getPKId($mPK);
	$this->fail($mPK === null
		? _WT('weeDatabase::getPKId should throw an IllegalStateException when no sequence value were generated since the beginning of the session.')
		: sprintf(_WT('weeDatabase::getPKId should throw an IllegalStateException when no value were generated for the sequence "%s" since the beginning of the session.'), $mPK)
	);
} catch (IllegalStateException $e) {}

$oDb->query('INSERT INTO ' . (isset($sTable) ? $oDb->escapeIdent($sTable) : 'getpkid') . ' (pk_value) VALUES (?)', -1);
$iPrevious = $oDb->getPKId($mPK);

$sMsg = $mPK === null
	? _WT('weeDatabase::getPKId did not return the expected previous sequence value + 1')
	: sprintf(_WT('weeDatabase::getPKId did not return the expected previous value + 1 for the sequence "%s".'), $mPK);

for ($i = 0; $i < 10; $i++) {
	$oDb->query('INSERT INTO ' . (isset($sTable) ? $oDb->escapeIdent($sTable) : 'getpkid') . ' (pk_value) VALUES (?)', $i);
	$iCurrent = $oDb->getPKId($mPK);
	$this->isEqual($iPrevious + 1, $iCurrent, $sMsg);
	$iPrevious = $iCurrent;
}
