<?php

require(dirname(__FILE__) . '/connect.php.inc');

$oDb->query('
	CREATE TEMPORARY TABLE numaffectedrows (
		nar_id INTEGER PRIMARY KEY,
		nar_value INTEGER
	);
');

try {
	for ($i = 0; $i < 100; ++$i)
		$oDb->query('INSERT INTO numaffectedrows (nar_value) VALUES (?)', $i);

	// weeSQLiteDatabase::numAffectedRows

	$this->isEqual(1, $oDb->numAffectedRows(),
		_WT('weeSQLiteDatabase::numAffectedRows should return 1 when the last query was an INSERT statement.'));

	$oDb->query('UPDATE numaffectedrows SET nar_value=? WHERE nar_value>=80', -1);
	$this->isEqual(20, $oDb->numAffectedRows(),
		_WT('weeSQLiteDatabase::numAffectedRows should return the number of updated rows when the last query was an UPDATE statement.'));

	$oDb->query('DELETE FROM numaffectedrows WHERE nar_value!=?', -1);
	$this->isEqual(80, $oDb->numAffectedRows(),
		_WT('weeSQLiteDatabase::numAffectedRows should return the number of deleted rows when the last query was a DELETE statement.'));
} catch (Exception $oException) {}

$oDb->query('DROP TABLE numaffectedrows');
if (isset($oException))
	throw $oException;
