<?php

require(dirname(__FILE__) . '/../../../oracle/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('CREATE TABLE "dbmeta" ("a" TIMESTAMP)');

try {
	$oColumn = $oMeta->table('dbmeta')->column('a');

	$this->isFalse($oColumn->hasValidator(),
		_WT('weeOracleDbMetaColumn::hasValidator should return false when the type is be handled by DbMeta.'));

	try {
		$oColumn->getValidator();
		$this->fail(_WT('weeOracleDbMetaColumn should throw an UnhandledTypeException when the type cannot be handled by dbmeta.'));
	} catch (UnhandledTypeException $e) {}
} catch (Exception $eException) {}

$oDb->query('DROP TABLE "dbmeta"');
if (isset($eException))
	throw $eException;

