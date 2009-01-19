<?php

require(dirname(__FILE__) . '/../../../mysql/connect.php.inc');
$oMeta = $oDb->meta();

try {
	$oDb->query('CREATE TABLE IF NOT EXISTS dbmeta (a timestamp)');

	$oColumn = $oMeta->table('dbmeta')->column('a');

	$this->isFalse($oColumn->hasValidator(),
		_WT('weeMySQLDbMetaColumn::hasValidator should return false when the type is be handled by DbMeta.'));

	try {
		$oColumn->getValidator();
		$this->fail(_WT('weeMySQLDbMetaColumn should throw an UnhandledTypeException when the type cannot be handled by dbmeta.'));
	} catch (UnhandledTypeException $e) {}
} catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS dbmeta');
if (isset($oException))
	throw $oException;
