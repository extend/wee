<?php

require('connect.php.inc');

// Create the test table

try {
	$oDb->query('
		CREATE GLOBAL TEMPORARY TABLE NUMAFFECTEDROWS (
			NAR_ID INTEGER,
			NAR_VALUE INTEGER
		)
	');
} catch (DatabaseException $e) {
	// This is expected to fail if the temporary table already exist
}

// Insert values into the test table

$sInsert = 'INSERT INTO NUMAFFECTEDROWS (NAR_ID, NAR_VALUE) VALUES';
for ($i = 0; $i < 100; $i++)
	$oDb->query($sInsert . ' (' . $oDb->escape($i) . ',' . $oDb->escape($i) . ')');

// Check weeOracleDatabase::numAffectedRows by checking our INSERT

$this->isEqual(1, $oDb->numAffectedRows(),
	_WT('The number of rows affected by our INSERT is wrong.'));

// Then do UPDATE and DELETE queries while doing more tests

$oDb->query('UPDATE NUMAFFECTEDROWS SET NAR_VALUE=? WHERE NAR_VALUE>=80', -1);
$this->isEqual(20, $oDb->numAffectedRows(),
	_WT('The number of rows affected by our UPDATE of the nar_value field is wrong.'));

$oDb->query('DELETE FROM NUMAFFECTEDROWS WHERE NAR_VALUE!=?', -1);
$this->isEqual(80, $oDb->numAffectedRows(),
	_WT('The number of rows affected by our DELETE of the rows with nar_value!=-1 is wrong.'));
