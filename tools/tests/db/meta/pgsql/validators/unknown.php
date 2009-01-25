<?php

require(dirname(__FILE__) . '/../../../pgsql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('START TRANSACTION');
try {
	$oDb->query('CREATE TABLE dbmeta (a boolean)');

	$oColumn = $oMeta->table('dbmeta')->column('a');

	$this->isFalse($oColumn->hasValidator(),
		_WT('weePgSQLDbMetaColumn::hasValidator should return false when the type is be handled by DbMeta.'));

	try {
		$oColumn->getValidator();
		$this->fail(_WT('weePgSQLDbMetaColumn should throw an UnhandledTypeException when the type cannot be handled by dbmeta.'));
	} catch (UnhandledTypeException $e) {}
} catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
