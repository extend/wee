<?php

require(dirname(__FILE__) . '/connect.php.inc');

$this->isInstanceOf($oDb->meta(), 'weePgSQLDbMeta',
	_WT('weePDODatabase::meta should return an instance of weePgSQLDbMeta when the current driver is "pgsql".'));
