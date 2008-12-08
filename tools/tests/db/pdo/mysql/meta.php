<?php

require(dirname(__FILE__) . '/connect.php.inc');

$this->isInstanceOf($oDb->meta(), 'weeMySQLDbMeta',
	_WT('weePDODatabase::meta should return an instance of weeMySQLDbMeta when the current driver is "mysql".'));
