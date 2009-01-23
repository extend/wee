<?php

$this->isTrue(isset($oDb), sprintf(_WT('The file %s requires a database object $oDb in the current scope before it is included.'), __FILE__));

// Create the test table

$oDb->query('
	CREATE TEMPORARY TABLE numaffectedrows (
		nar_value INTEGER
	)
');

try {
	// Insert values into the test table

	for ($i = 0; $i < 100; $i++)
		$oDb->query('INSERT INTO numaffectedrows (nar_value) VALUES(?)', $i);

	$this->isEqual(1, $oDb->numAffectedRows(),
		_WT('The number of rows affected by our last INSERT is wrong.'));

	// Do UPDATE and DELETE queries while doing more tests

	$oDb->query('UPDATE numaffectedrows SET nar_value = ? WHERE nar_value >= 80', -1);
	$this->isEqual(20, $oDb->numAffectedRows(),
		_WT('The number of rows affected by our UPDATE of the nar_value field is wrong.'));

	$oDb->query('DELETE FROM numaffectedrows WHERE nar_value != ?', -1);
	$this->isEqual(80, $oDb->numAffectedRows(),
		_WT('The number of rows affected by our DELETE of the rows with nar_value!=-1 is wrong.'));
} catch (Exception $oException) {}

$oDb->query('DROP TABLE numaffectedrows');
if (isset($oException))
	throw $oException;