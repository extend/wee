<?php

if (!isset($oDb))
	require('connect.php.inc');

$oDb->query('
	CREATE TABLE #getpkid (
		pk_id int IDENTITY PRIMARY KEY,
		pk_value int
	)
');

try {
	$mPK = null;
	$sTable = '#getpkid';
	require(dirname(__FILE__) . '/../getpkid.php.inc');
} catch (Exception $e) {}

$oDb->query('DROP TABLE #getpkid');
if (isset($eException))
	throw $eException;
