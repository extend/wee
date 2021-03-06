<?php

try {
	$oDb->selectDb('database_which_does_not_exist');
	$this->fail(_WT('weeMSSQLDatabase::selectDb should throw a DatabaseException when the given database does not exist.'));
} catch (DatabaseException $e) {}

try {
	$oDb->selectDb($sCurrentDb);
} catch (DatabaseException $e) {
	$this->fail(_WT('weeMSSQLDatabase::selectDb should not throw a DatabaseException when the given database can be selected.'));
}
