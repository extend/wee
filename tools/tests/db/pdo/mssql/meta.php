<?php

require('connect.php.inc');
$this->isInstanceOf($oDb->meta(), 'weeMSSQLDbMeta',
	_WT('weePDODatabase::meta should return an instance of weeMSSQLDbMeta when the current driver is "mssql".'));
