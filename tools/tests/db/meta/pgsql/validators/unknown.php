<?php

require(dirname(__FILE__) . '/../../../pgsql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('START TRANSACTION');
try {
	$oDb->query('CREATE TABLE dbmeta (a boolean)');

	try {
		$oMeta->table('dbmeta')->column('a')->getValidator();
		$this->fail('weePgSQLDbMetaColumn should throw an UnhandledTypeException when the type cannot be handled by dbmeta.');
	} catch (UnhandledTypeException $e) {}
} catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
