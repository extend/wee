<?php

require(dirname(__FILE__) . '/../../../sqlite/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('CREATE TABLE dbmeta (a)');

try {
	try {
		$oMeta->table('dbmeta')->column('a')->getValidator();
		$this->fail('weeSQLiteDbMetaColumn should throw an UnhandledTypeException when the type cannot be handled by dbmeta.');
	} catch (UnhandledTypeException $e) {}
} catch (Exception $oException) {}

$oDb->query('DROP TABLE dbmeta');
if (isset($oException))
	throw $oException;
