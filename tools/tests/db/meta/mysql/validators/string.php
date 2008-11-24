<?php

require(dirname(__FILE__) . '/../../../mysql/connect.php.inc');
$oMeta = $oDb->meta();

try
{
	$oDb->query("CREATE TABLE IF NOT EXISTS dbmeta (
		a char(42),
		b varchar(42)
	)");
	$oTable = $oMeta->table('dbmeta');

	// char

	try {
		$o = $oTable->column('a')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the column is "char".'));
	}

	$this->isInstanceOf($o, 'weeStringValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeStringValidator when the type of the column is "char".'));

	$this->isEqual(array('max' => 42), $o->getArgs(),
		_WT('The arguments of the char validator are not correct.'));

	// varchar

	try {
		$o = $oTable->column('b')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the column is "varchar".'));
	}

	$this->isInstanceOf($o, 'weeStringValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeStringValidator when the type of the column is "varchar".'));

	$this->isEqual(array('max' => 42), $o->getArgs(),
		_WT('The arguments of the varchar validator are not correct.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS dbmeta');
if (isset($oException))
	throw $oException;
