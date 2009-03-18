<?php

if (!isset($oDb))
	require('connect.php.inc');

$oDb->query('
	CREATE TEMPORARY TABLE getpkid (
		pk_id SERIAL NOT NULL,
		pk_value INTEGER
	);
');

try {
	$mPK = null;
	require(dirname(__FILE__) . '/../getpkid.php.inc');
} catch (Exception $oException) {}

$oDb->query('DROP TABLE getpkid');
if (isset($oException))
	throw $oException;
