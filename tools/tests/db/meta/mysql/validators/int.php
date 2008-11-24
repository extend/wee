<?php

require(dirname(__FILE__) . '/../../../mysql/connect.php.inc');
$oMeta = $oDb->meta();

try
{
	$oDb->query('CREATE TABLE IF NOT EXISTS dbmeta (
		a tinyint,
		b tinyint		unsigned,
		c smallint,
		d smallint		unsigned,
		e mediumint,
		f mediumint		unsigned,
		g int,
		h int			unsigned,
		i bigint,
		j bigint		unsigned
	)');
	$oTable = $oMeta->table('dbmeta');

	// tinyint

	try {
		$o = $oTable->column('a')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "tinyint".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "tinyint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The the tinyint validator should expect integers.'));

	$this->isEqual(- 256 / 2, $aArgs['min'],
		_WT('The `min` argument of the tinyint validator is not correct.'));

	$this->isEqual(256 / 2 - 1, $aArgs['max'],
		_WT('The `max` argument of the tinyint validator is not correct.'));

	// tinyint unsigned

	$aArgs = $oTable->column('b')->getValidator()->getArgs();

	$this->isEqual(0, $aArgs['min'],
		_WT('The `min` argument of tinyint unsigned validator is not correct.'));

	$this->isEqual(256 - 1, $aArgs['max'],
		_WT('The `max` argument of tinyint unsigned validator is not correct.'));

	// smallint

	try {
		$o = $oTable->column('c')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "smallint".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "smallint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The the smallint validator should expect integers.'));

	$this->isEqual(- pow(256, 2) / 2, $aArgs['min'],
		_WT('The `min` argument of the smallint validator is not correct.'));

	$this->isEqual(pow(256, 2) / 2 - 1, $aArgs['max'],
		_WT('The `max` argument of the smallint validator is not correct.'));

	// smallint unsigned

	$aArgs = $oTable->column('d')->getValidator()->getArgs();

	$this->isEqual(0, $aArgs['min'],
		_WT('The `min` argument of smallint unsigned validator is not correct.'));

	$this->isEqual(pow(256, 2) - 1, $aArgs['max'],
		_WT('The `max` argument of smallint unsigned validator is not correct.'));

	// mediumint

	try {
		$o = $oTable->column('e')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "mediumint".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "mediumint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The the mediumint validator should expect integers.'));

	$this->isEqual(- pow(256, 3) / 2, $aArgs['min'],
		_WT('The `min` argument of the mediumint validator is not correct.'));

	$this->isEqual(pow(256, 3) / 2 - 1, $aArgs['max'],
		_WT('The `max` argument of the mediumint validator is not correct.'));

	// mediumint unsigned

	$aArgs = $oTable->column('f')->getValidator()->getArgs();

	$this->isEqual(0, $aArgs['min'],
		_WT('The `min` argument of mediumint unsigned validator is not correct.'));

	$this->isEqual(pow(256, 3) - 1, $aArgs['max'],
		_WT('The `max` argument of mediumint unsigned validator is not correct.'));

	// int

	try {
		$o = $oTable->column('g')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "int".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "int".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The the int validator should expect integers.'));

	$this->isEqual(- pow(256, 4) / 2, $aArgs['min'],
		_WT('The `min` argument of the int validator is not correct.'));

	$this->isEqual(pow(256, 4) / 2 - 1, $aArgs['max'],
		_WT('The `max` argument of the int validator is not correct.'));

	// int unsigned

	$o = $oTable->column('h')->getValidator();

	$this->isInstanceOf($o, 'weeBigNumberValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeBigNumberValidator when the type of the column is "int unsigned".'));

	$aArgs = $o->getArgs();

	$this->isEqual('0', $aArgs['min'],
		_WT('The `min` argument of int unsigned validator is not correct.'));

	if (function_exists('bcpow'))
		$this->isEqual(bcsub(bcpow(256, 4), 1), $aArgs['max'],
			_WT('The `max` argument of int unsigned validator is not correct.'));

	// bigint

	try {
		$o = $oTable->column('i')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "bigint".'));
	}

	$this->isInstanceOf($o, 'weeBigNumberValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeBigNumberValidator when the type of the column is "bigint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The the bigint validator should expect integers.'));

	if (function_exists('bcpow')) {
		$this->isEqual(bcsub(0, bcdiv(bcpow(256, 8), 2)), $aArgs['min'],
			_WT('The `min` argument of the bigint validator is not correct.'));

		$this->isEqual(bcsub(bcdiv(bcpow(256, 8), 2), 1), $aArgs['max'],
			_WT('The `max` argument of the bigint validator is not correct.'));
	}

	if (function_exists('bcpow')) {
		// bigint unsigned

		$aArgs = $oTable->column('j')->getValidator()->getArgs();

		$this->isEqual('0', $aArgs['min'],
			_WT('The `min` argument of bigint unsigned validator is not correct.'));

		$this->isEqual(bcsub(bcpow(256, 8), 1), $aArgs['max'],
			_WT('The `max` argument of bigint unsigned validator is not correct.'));
	}
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS dbmeta');
if (isset($oException))
	throw $oException;
