<?php

require('connect.php.inc');

$oDb->query('
	CREATE TEMPORARY TABLE getpkid (
		pk_id SERIAL NOT NULL,
		pk_value INTEGER
	);
');

$mPK = 'getpkid_pk_id_seq';

try {
	require(dirname(__FILE__) . '/../getpkid.php.inc');
} catch (Exception $oException) {}

$oDb->query('DROP TABLE getpkid');

if (isset($oException))
	throw $oException;
