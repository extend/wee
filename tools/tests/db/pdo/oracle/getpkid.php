<?php

require('connect.php.inc');

// Create the test table

try {
	$oDb->query('
		CREATE GLOBAL TEMPORARY TABLE GETPKID (
			PK_ID INTEGER PRIMARY KEY,
			PK_VALUE INTEGER
		)
	');

	$oDb->query('CREATE SEQUENCE GETPKID_SEQ');

	$oDb->query('
		CREATE TRIGGER GETPKID_TRIGGER
		BEFORE INSERT ON GETPKID FOR EACH ROW
		BEGIN
			IF (:NEW.PK_ID IS NULL) THEN
				SELECT GETPKID_SEQ.NEXTVAL INTO :NEW.PK_ID FROM DUAL;
			END IF;
		END;
	');
} catch (DatabaseException $e) {
	// This is expected to fail if the temporary table already exist
}

// Insert a row and check each time if our primary key id is incrementing
// The name of the sequence used is the default name for the sequence when using SERIAL

$oDb->query('INSERT INTO GETPKID (PK_VALUE) VALUES (?)', -1);
$iPrevious = $oDb->getPKId('GETPKID_SEQ');

for ($i = 0; $i < 100; $i++)
{
	$oDb->query('INSERT INTO GETPKID (PK_VALUE) VALUES (?)', $i);
	$iCurrent = $oDb->getPKId('GETPKID_SEQ');

	$this->isEqual($iPrevious + 1, $iCurrent,
		sprintf(_WT('The primary key id returned by getPKId (%d) ' .
		'is not equal as the previous + 1 (%d + 1).'), $iCurrent, $iPrevious));

	$iPrevious = $iCurrent;
}
