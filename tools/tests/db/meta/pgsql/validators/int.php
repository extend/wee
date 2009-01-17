<?php

require(dirname(__FILE__) . '/../../../pgsql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('START TRANSACTION');
try
{
	$oDb->query('CREATE TABLE dbmeta (
		a smallint,
		b int,
		c bigint
	)');
	$oTable = $oMeta->table('dbmeta');

	// smallint

	$oColumn = $oTable->column('a');

	$this->isTrue($oColumn->hasValidator(),
		sprintf(_WT('weePgSQLDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), 'smallint'));

	try {
		$o = $oColumn->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weePgSQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "smallint".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weePgSQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "smallint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The smallint validator should expect integers.'));

	$this->isEqual(- pow(256, 2) / 2, $aArgs['min'],
		_WT('The `min` argument of the smallint validator is not correct.'));

	$this->isEqual(pow(256, 2) / 2 - 1, $aArgs['max'],
		_WT('The `max` argument of the smallint validator is not correct.'));

	// int

	$oColumn = $oTable->column('b');

	$this->isTrue($oColumn->hasValidator(),
		sprintf(_WT('weePgSQLDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), 'int'));

	try {
		$o = $oColumn->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weePgSQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "int".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weePgSQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "int".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The the int validator should expect integers.'));

	$this->isEqual(- pow(256, 4) / 2, $aArgs['min'],
		_WT('The `min` argument of the int validator is not correct.'));

	$this->isEqual(pow(256, 4) / 2 - 1, $aArgs['max'],
		_WT('The `max` argument of the int validator is not correct.'));

	// bigint

	$oColumn = $oTable->column('c');

	$this->isTrue($oColumn->hasValidator(),
		sprintf(_WT('weePgSQLDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), 'bigint'));

	try {
		$o = $oColumn->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weePgSQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "bigint".'));
	}

	$this->isInstanceOf($o, 'weeBigNumberValidator',
		_WT('weePgSQLDbMetaColumn::getValidator should return an instance of weeBigNumberValidator when the type of the column is "bigint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The bigint validator should expect integers.'));

	if (function_exists('bcpow')) {
		$this->isEqual(bcsub(0, bcdiv(bcpow(256, 8), 2)), $aArgs['min'],
			_WT('The `min` argument of the bigint validator is not correct.'));

		$this->isEqual(bcsub(bcdiv(bcpow(256, 8), 2), 1), $aArgs['max'],
			_WT('The `max` argument of the bigint validator is not correct.'));
	}
}
catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
