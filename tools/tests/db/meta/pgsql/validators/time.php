<?php

require(dirname(__FILE__) . '/../../../pgsql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('START TRANSACTION');
try
{
	$oDb->query('CREATE TABLE dbmeta (
		a time
	)');
	$oTable = $oMeta->table('dbmeta');

	// time

	$oColumn = $oTable->column('a');

	$this->isTrue($oColumn->hasValidator(),
		sprintf(_WT('weePgSQLDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), 'time'));

	try {
		$o = $oColumn->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weePgSQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the column is "time".'));
	}

	$this->isInstanceOf($o, 'weeTimeValidator',
		_WT('weePgSQLDbMetaColumn::getValidator should return an instance of weeTimeValidator when the type of the column is "time".'));
}
catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
