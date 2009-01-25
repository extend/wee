<?php

require('connect.php.inc');

$oDb->query('
	CREATE TEMPORARY TABLE getpkid (
		pk_id INTEGER PRIMARY KEY,
		pk_value INTEGER
	);
');

$mPK = null;

try {
	require(dirname(__FILE__) . '/../getpkid.php.inc');
} catch (Exception $oException) {}

$oDb->query('DROP TABLE getpkid');

if (isset($oException))
	throw $oException;
