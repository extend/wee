<?php

require(dirname(__FILE__) . '/../../sqlite/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('CREATE TABLE dbmeta (a)');

try
{
	// weeSQLiteDbMeta::tables

	$aTablesNames = array();
	foreach ($oMeta->tables() as $oTable)
		$aTablesNames[] = $oTable->name();

	$this->isEqual(array('dbmeta'), $aTablesNames,
		_WT('weeSQLiteDbMeta::tables did not return the expected table.'));

	// weeSQLiteDbMeta::tablesNames

	$this->isEqual(array('dbmeta'), $oMeta->tablesNames(),
		_WT("weeSQLiteDbMeta::tablesNames does not return the expected tables' names."));

	// weeSQLiteDbMeta::tableExists

	$this->isTrue($oMeta->tableExists('dbmeta'),
		_WT('weeSQLiteDbMeta::tableExists should return true when the given table name is found in the database.'));

	$this->isFalse($oMeta->tableExists('not_found'),
		_WT('weeSQLiteDbMeta::tableExists should return false when the given table name is not found in the database.'));

	// weeSQLiteDbMeta::table

	try {
		$oMeta->table('lhc');
		$this->fail('weeSQLiteDbMeta::table should throw an UnexpectedValueException when requesting a table which does not exist in the database.');
	} catch (UnexpectedValueException $e) {}

	$oTable = $oMeta->table('dbmeta');

	// weeSQLiteDbMetaTable::name

	$this->isEqual('dbmeta', $oTable->name(),
		_WT('weeSQLiteDbMeta::table does not return the requested table.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE dbmeta');
if (isset($oException))
	throw $oException;
