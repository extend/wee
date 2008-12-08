<?php

require(dirname(__FILE__) . '/connect.php.inc');

$this->isInstanceOf($oDb->meta(), 'weeSQLiteDbMeta',
	_WT('weePDODatabase::meta should return an instance of weeSQLiteDbMeta when the current driver is "sqlite2".'));
