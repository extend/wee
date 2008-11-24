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

	try {
		$o = $oTable->column('a')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weePgSQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "smallint".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weePgSQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "smallint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The the smallint validator should expect integers.'));

	$this->isEqual(- pow(256, 2) / 2, $aArgs['min'],
		_WT('The `min` argument of the smallint validator is not correct.'));

	$this->isEqual(pow(256, 2) / 2 - 1, $aArgs['max'],
		_WT('The `max` argument of the smallint validator is not correct.'));

	// int

	try {
		$o = $oTable->column('b')->getValidator();
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

	try {
		$o = $oTable->column('c')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weePgSQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "bigint".'));
	}

	$this->isInstanceOf($o, 'weeBigNumberValidator',
		_WT('weePgSQLDbMetaColumn::getValidator should return an instance of weeBigNumberValidator when the type of the column is "bigint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The the bigint validator should expect integers.'));

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
