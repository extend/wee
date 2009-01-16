<?php

require(dirname(__FILE__) . '/../../../pgsql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('START TRANSACTION');
try
{
	$oDb->query('CREATE TABLE dbmeta (
		a char(42),
		b varchar(42),
		c varchar
	)');
	$oTable = $oMeta->table('dbmeta');

	// char

	$oColumn = $oTable->column('a');

	$this->isTrue($oColumn->hasValidator(),
		sprintf(_WT('weePgSQLDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), 'char'));

	try {
		$o = $oColumn->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weePgSQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the column is "char".'));
	}

	$this->isInstanceOf($o, 'weeStringValidator',
		_WT('weePgSQLDbMetaColumn::getValidator should return an instance of weeStringValidator when the type of the column is "char".'));

	$this->isEqual(array('max' => 42), $o->getArgs(),
		_WT('The arguments of the char validator are not correct.'));

	// varchar

	$oColumn = $oTable->column('b');

	$this->isTrue($oColumn->hasValidator(),
		sprintf(_WT('weePgSQLDbMetaColumn::hasValidator should return true when the type of the column is "%s".'), 'varchar'));

	try {
		$o = $oColumn->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weePgSQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the column is "varchar".'));
	}

	$this->isInstanceOf($o, 'weeStringValidator',
		_WT('weePgSQLDbMetaColumn::getValidator should return an instance of weeStringValidator when the type of the column is "varchar".'));

	$this->isEqual(array('max' => 42), $o->getArgs(),
		_WT('The arguments of the varchar validator are not correct.'));

	// unbounded varchar

	$this->isEqual(array(), $oTable->column('c')->getValidator()->getArgs(),
		_WT('The arguments of the unbounded varchar validator are not correct.'));
}
catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
