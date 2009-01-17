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

	$oColumn = $oTable->column('a');

	$this->isTrue($oColumn->hasValidator(),
		sprintf(_WT('weeMySQLDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), 'tinyint'));

	try {
		$o = $oColumn->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "tinyint".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "tinyint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The tinyint validator should expect integers.'));

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

	$oColumn = $oTable->column('c');

	$this->isTrue($oColumn->hasValidator(),
		sprintf(_WT('weeMySQLDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), 'smallint'));

	try {
		$o = $oColumn->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "smallint".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "smallint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The smallint validator should expect integers.'));

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

	$oColumn = $oTable->column('e');

	$this->isTrue($oColumn->hasValidator(),
		sprintf(_WT('weeMySQLDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), 'mediumint'));

	try {
		$o = $oColumn->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "mediumint".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "mediumint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The mediumint validator should expect integers.'));

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

	$oColumn = $oTable->column('g');

	$this->isTrue($oColumn->hasValidator(),
		sprintf(_WT('weeMySQLDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), 'int'));

	try {
		$o = $oColumn->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "int".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "int".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The int validator should expect integers.'));

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

	$oColumn = $oTable->column('i');

	$this->isTrue($oColumn->hasValidator(),
		sprintf(_WT('weeMySQLDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), 'bigint'));

	try {
		$o = $oColumn->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "bigint".'));
	}

	$this->isInstanceOf($o, 'weeBigNumberValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeBigNumberValidator when the type of the column is "bigint".'));

	$aArgs = $o->getArgs();

	$this->isEqual('int', $aArgs['format'],
		_WT('The bigint validator should expect integers.'));

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
