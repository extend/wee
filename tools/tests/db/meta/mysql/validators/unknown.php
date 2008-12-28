<?php

require(dirname(__FILE__) . '/../../../mysql/connect.php.inc');
$oMeta = $oDb->meta();

try {
	$oDb->query('CREATE TABLE IF NOT EXISTS dbmeta (a timestamp)');

	try {
		$oMeta->table('dbmeta')->column('a')->getValidator();
		$this->fail('weeMySQLDbMetaColumn should throw an UnhandledTypeException when the type cannot be handled by dbmeta.');
	} catch (UnhandledTypeException $e) {}
} catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS dbmeta');
if (isset($oException))
	throw $oException;
