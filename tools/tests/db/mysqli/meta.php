<?php

require(dirname(__FILE__) . '/connect.php.inc');

$this->isInstanceOf($oDb->meta(), 'weeMySQLDbMeta',
	_WT('weeMySQLiDatabase::meta should return an instance of weeMySQLDbMeta.'));
