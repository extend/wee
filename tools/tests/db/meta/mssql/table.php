<?php

require(dirname(__FILE__) . '/../../mssql/connect.php.inc');
$oMeta		= $oDb->meta();
$oCurrent	= $oMeta->currentSchema();

$oDb->query('BEGIN TRANSACTION');

try
{
	$oDb->query('CREATE TABLE test1 (a int)');
	$oDb->query('CREATE SCHEMA pikachu CREATE TABLE test1 (b int) CREATE TABLE test2 (a int)');
	$oDb->query("EXEC sp_addextendedproperty MS_Description, 'down the road', 'SCHEMA', pikachu, 'TABLE', test1");

	// weeMSSQLDbMeta::tables

	$bTableFound	= false;
	$aTablesNames	= array();
	foreach ($oMeta->tables() as $oTable)
	{
		if ($oTable->name() == 'test1')
		{
			if ($bTableFound)
				$this->burn(_WT('weeMSSQLDbMeta::tables returned two tables named "test1".'));

			$this->isEqual($oCurrent->name(), $oTable->schemaName(),
				_WT('weeMSSQLDbMeta::tables did not return the table "test1" from the current schema.'));

			$bTableFound = true;
		}
		else
			$this->isNotEqual('test2', $oTable->name(),
				_WT('weeMSSQLDbMeta::tables returned a table "test2" which was created in a different schema than the current.'));

		$aTablesNames[] = $oTable->name();
	}

	$this->isTrue($bTableFound,
		_WT('weeMSSQLDbMeta::tables did not return the expected table "test1".'));

	// weeMSSQLDbMeta::tablesNames

	$this->isEqual($aTablesNames, $oMeta->tablesNames(),
		_WT('weeMSSQLDbMeta::tablesNames did not return the same tables as the tables method.'));

	// weeMSSQLDbMeta::tableExists

	$this->isTrue($oMeta->tableExists('test1'),
		_WT('weeMSSQLDbMeta::tableExists should return true when the given table name is found in the database and the table is visible.'));

	$this->isFalse($oMeta->tableExists('not_found'),
		_WT('weeMSSQLDbMeta::tableExists should return false when the given table name is not found in the database.'));

	$this->isFalse($oMeta->tableExists('test2'),
		_WT('weeMSSQLDbMeta::tableExists should return false when the given table name is found in the database but the table is invisible.'));

	// weeMSSQLDbMeta::table

	try {
		$oMeta->table('lhc');
		$this->fail(_WT('weeMSSQLDbMeta::table should throw an UnexpectedValueException when requesting a table which does not exist in the database.'));
	} catch (UnexpectedValueException $e) {}

	try {
		$oMeta->table('test2');
		$this->fail(_WT('weeMSSQLDbMeta::table should throw an UnexpectedValueException when requesting an invisible table.'));
	} catch (UnexpectedValueException $e) {}

	$oTable = $oMeta->table('test1');

	// weeMSSQLDbMetaTable::schemaName

	$this->isEqual($oCurrent->name(), $oTable->schemaName(),
		_WT('weeMSSQLDbMeta::table does not return table from the correct schema.'));

	// weeMSSQLDbMetaTable::name

	$this->isEqual('test1', $oTable->name(),
		_WT('weeMSSQLDbMeta::table does not return the requested table.'));

	// weeMSSQLDbMetaTable::comment

	$this->isNull($oTable->comment(),
		_WT('weeMSSQLDbMeta::comment should return null when the table does not have a comment.'));

	$this->isEqual('down the road', $oMeta->schema('pikachu')->table('test1')->comment(),
		_WT('weeMSSQLDbMeta::comment does not return the comment of the table.'));
}
catch (Exception $eException) {}

$oDb->query('ROLLBACK');
if (isset($eException))
	throw $eException;
