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
	require(dirname(__FILE__) . '/../getpkid.php.inc');
} catch (Exception $eException) {}

$oDb->query('DROP TABLE getpkid');

if (isset($eException))
	throw $eException;
