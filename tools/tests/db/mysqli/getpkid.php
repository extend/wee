<?php

require('connect.php.inc');

// Create the test table

$oDb->query('
	CREATE TEMPORARY TABLE getpkid (
		pk_id SERIAL NOT NULL,
		pk_value INTEGER
	);
');

// Insert a row and check each time if our primary key id is incrementing
// The name of the sequence used is the default name for the sequence when using SERIAL

$oDb->query('INSERT INTO getpkid (pk_value) VALUES (?)', -1);
$iPrevious = $oDb->getPKId('getpkid_pk_id_seq');

for ($i = 0; $i < 100; $i++)
{
	$oDb->query('INSERT INTO getpkid (pk_value) VALUES (?)', $i);
	$iCurrent = $oDb->getPKId('getpkid_pk_id_seq');

	$this->isEqual($iPrevious + 1, $iCurrent,
		sprintf(_WT('The primary key id returned by getPKId (%d) is not equal to the previous + 1 (%d + 1).'), $iCurrent, $iPrevious));

	$iPrevious = $iCurrent;
}
