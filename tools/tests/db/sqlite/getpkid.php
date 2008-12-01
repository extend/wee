<?php

require(dirname(__FILE__) . '/connect.php.inc');

$oDb->query('
	CREATE TEMPORARY TABLE getpkid (
		pk_id INTEGER PRIMARY KEY,
		pk_value INTEGER
	);
');

try {
	for ($i = 0; $i < 100; $i++) {
		$oDb->query('INSERT INTO getpkid (pk_value) VALUES (?)', $i);

		$this->isEqual($i + 1, $oDb->getPKId(),
			_WT('weeSQLiteDatabase::getPKId should return the value of the last primary key value inserted.'));
	}
} catch (Exception $oException) {}

$oDb->query('DROP TABLE getpkid');
if (isset($oException))
	throw $oException;
