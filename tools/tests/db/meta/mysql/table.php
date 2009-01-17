<?php

require(dirname(__FILE__) . '/../../mysql/connect.php.inc');
$oMeta = $oDb->meta();

try
{
	$oDb->query("CREATE TABLE IF NOT EXISTS dbmeta (a integer) COMMENT = 'Tests are marvelous.'");

	// weeMySQLDbMeta::tables

	$aTablesNames = array();
	foreach ($oMeta->tables() as $oTable)
		$aTablesNames[] = $oTable->name();

	$this->isEqual(array('dbmeta'), $aTablesNames,
		_WT('weeMySQLDbMeta::tables does not return the expected tables.'));

	// weeMySQLDbMeta::tablesNames

	$this->isEqual(array('dbmeta'), $oMeta->tablesNames(),
		_WT("weeMySQLDbMeta::tablesNames does not return the expected tables' names."));

	// weeMySQLDbMeta::tableExists

	$this->isTrue($oMeta->tableExists('dbmeta'),
		_WT('weeMySQLDbMeta::tableExists should return true when the given table name is found in the database.'));

	$this->isFalse($oMeta->tableExists('not_found'),
		_WT('weeMySQLDbMeta::tableExists should return false when the given table name is not found in the database.'));

	// weeMySQLDbMeta::table

	try {
		$oMeta->table('lhc');
		$this->fail('weeMySQLDbMeta::table should throw an UnexpectedValueException when requesting a table which does not exist in the database.');
	} catch (UnexpectedValueException $e) {}

	$oTable = $oMeta->table('dbmeta');

	// weeMySQLDbMetaTable::name

	$this->isEqual('dbmeta', $oTable->name(),
		_WT('weeMySQLDbMeta::table does not return the requested table.'));

	// weeMySQLDbMetaTable::comment

	$this->isEqual('Tests are marvelous.', $oTable->comment(),
		_WT('weeMySQLDbMetaTable::comment does not correctly return the comment of the table.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS dbmeta');
if (isset($oException))
	throw $oException;
