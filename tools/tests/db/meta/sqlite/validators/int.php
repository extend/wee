<?php

require(dirname(__FILE__) . '/../../../sqlite/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('CREATE TABLE dbmeta (a INTEGER PRIMARY KEY)');

try
{
	$oTable = $oMeta->table('dbmeta');

	// INTEGER PRIMARY KEY

	try {
		$o = $oTable->column('a')->getValidator();
	} catch (UnhandledTypeException $e) {
		$this->fail(_WT('weeSQLiteDbMetaColumn::getValidator should not throw an UnhandledTypeException when the type of the the column is "INTEGER PRIMARY KEY".'));
	}

	$this->isInstanceOf($o, 'weeNumberValidator',
		_WT('weeSQLiteDbMetaColumn::getValidator should return an instance of weeNumberValidator when the type of the column is "INTEGER PRIMARY KEY".'));

	$this->isEqual(array('format' => 'int'), $o->getArgs(),
		_WT('The INTEGER PRIMARY KEY validator should expect integers without limits.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE dbmeta');
if (isset($oException))
	throw $oException;
