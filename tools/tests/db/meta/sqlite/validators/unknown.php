<?php

require(dirname(__FILE__) . '/../../../sqlite/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('CREATE TABLE dbmeta (a)');

try {
	$oColumn = $oMeta->table('dbmeta')->column('a');

	$this->isFalse($oColumn->hasValidator(),
		_WT('weePgSQLDbMetaColumn::hasValidator should return false when the type is be handled by DbMeta.'));

	try {
		$oColumn->getValidator();
		$this->fail('weeSQLiteDbMetaColumn should throw an UnhandledTypeException when the type cannot be handled by dbmeta.');
	} catch (UnhandledTypeException $e) {}
} catch (Exception $oException) {}

$oDb->query('DROP TABLE dbmeta');
if (isset($oException))
	throw $oException;
