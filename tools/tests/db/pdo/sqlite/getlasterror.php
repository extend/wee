<?php

require(dirname(__FILE__) . '/connect.php.inc');

// weePDODatabase::getLastError

try {
	$oDb->query('INVALID_SQL');
}
catch (DatabaseException $e) {
	$this->isNotNull($oDb->getLastError(),
		_WT('weePDODatabase::getLastError should return the message of the last error.'));
}
