<?php

require(dirname(__FILE__) . '/connect.php.inc');

try {
	$oDb->selectDb('database_which_does_not_exist');
	$this->fail(_WT('weeMySQLiDatabase::selectDb should throw a DatabaseException when the given database does not exist.'));
} catch (DatabaseException $e) {}

try {
	$oDb->selectDb($oDb->queryValue('SELECT DATABASE()'));
} catch (DatabaseException $e) {
	$this->fail(_WT('weeMySQLiDatabase::selectDb should not throw a DatabaseException when the given database can be selected.'));
}
