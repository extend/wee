<?php

require(dirname(__FILE__) . '/../../../pgsql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('START TRANSACTION');
try
{
	$oDb->query('CREATE TABLE dbmeta (
		a real
	)');
	$oTable = $oMeta->table('dbmeta');

	// real

	try {
		$o = $oTable->column('a')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weePgSQLDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the column is "real".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weePgSQLDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "real".'));

	$this->isEqual(array('format' => 'float'), $o->getArgs(),
		_WT('The arguments of the real validator are not correct.'));
}
catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
