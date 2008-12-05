<?php

require('connect.php.inc');

// Create the test table

$oDb->query('
	CREATE TEMPORARY TABLE numaffectedrows (
		nar_id SERIAL NOT NULL,
		nar_value INTEGER
	);
');

// Insert values into the test table

$sInsert = 'INSERT INTO numaffectedrows (nar_value) VALUES';
for ($i = 0; $i < 100; $i++)
	$sInsert .= ' (' . $oDb->escape($i) . '),';
$sInsert = substr($sInsert, 0, -1);

$oDb->query($sInsert);

// Check weeMySQLiDatabase::numAffectedRows by checking our INSERT

$this->isEqual(100, $oDb->numAffectedRows(),
	_WT('The number of rows affected by our INSERT of 100 rows is wrong.'));

// Then do UPDATE and DELETE queries while doing more tests

$oDb->query('UPDATE numaffectedrows SET nar_value=? WHERE nar_value>=80', -1);
$this->isEqual(20, $oDb->numAffectedRows(),
	_WT('The number of rows affected by our UPDATE of the nar_value field is wrong.'));

$oDb->query('DELETE FROM numaffectedrows WHERE nar_value!=?', -1);
$this->isEqual(80, $oDb->numAffectedRows(),
	_WT('The number of rows affected by our DELETE of the rows with nar_value!=-1 is wrong.'));
