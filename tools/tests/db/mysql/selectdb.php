<?php

if (!isset($oDb))
	require('connect.php.inc');
$sCurrentDb = $oDb->queryValue('SELECT DATABASE()');
require(dirname(__FILE__) . '/../selectdb.php.inc');
