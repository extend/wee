<?php

require(dirname(__FILE__) . '/../../../mysql/connect.php.inc');
$oMeta = $oDb->meta();

try
{
	$oDb->query("CREATE TABLE IF NOT EXISTS dbmeta (
		a date,
		b time
	)");
	$oTable = $oMeta->table('dbmeta');

	// date

	try {
		$o = $oTable->column('a')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the column is "date".'));
	}

	$this->isInstanceOf($o, 'weeDateValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeDateValidator when the type of the column is "char".'));

	$this->isEqual(array('min' => '1000-01-01', 'max' => '9999-12-31'), $o->getArgs(),
		_WT('The arguments of the date validator are not correct.'));

	// time

	try {
		$o = $oTable->column('b')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeMySQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the column is "time".'));
	}

	$this->isInstanceOf($o, 'weeTimeValidator',
		_WT('weeMySQLDbMetaColumn::getValidator should return an instance of weeTimeValidator when the type of the column is "time".'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS dbmeta');
if (isset($oException))
	throw $oException;
