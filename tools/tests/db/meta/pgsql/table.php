<?php

require(dirname(__FILE__) . '/../../pgsql/connect.php.inc');
$oMeta		= $oDb->meta();
$oCurrent	= $oMeta->currentSchema();

$oDb->query('BEGIN');

try
{
	$oDb->query('CREATE TABLE test1 ()');
	$oDb->query('CREATE SCHEMA pikachu CREATE TABLE test1 () CREATE TABLE test2 ()');
	$oDb->query("COMMENT ON TABLE test1 IS 'Tests are marvelous.'");

	// weePgSQLDbMeta::tables

	$bTableFound	= false;
	$aTablesNames	= array();
	foreach ($oMeta->tables() as $oTable)
	{
		if ($oTable->name() == 'test1')
		{
			if ($bTableFound)
				$this->burn(_WT('weePgSQLDbMeta::tables returned two tables named "test1".'));

			$this->isEqual($oCurrent->name(), $oTable->schemaName(),
				_WT('weePgSQLDbMeta::tables did not return the table "test1" from the current schema.'));

			$bTableFound = true;
		}
		else
			$this->isNotEqual('test2', $oTable->name(),
				_WT('weePgSQLDbMeta::tables returned a table "test2" which was created in a different schema than the current.'));

		$aTablesNames[] = $oTable->name();
	}

	$this->isTrue($bTableFound,
		_WT('weePgSQLDbMeta::tables did not return the expected table "test1".'));

	// weePgSQLDbMeta::tablesNames

	$this->isEqual($aTablesNames, $oMeta->tablesNames(),
		_WT('weePgSQLDbMeta::tablesNames did not return the same tables as the tables method.'));

	// weePgSQLDbMeta::tableExists

	$this->isTrue($oMeta->tableExists('test1'),
		_WT('weePgSQLDbMeta::tableExists should return true when the given table name is found in the database and the table is visible.'));

	$this->isFalse($oMeta->tableExists('not_found'),
		_WT('weePgSQLDbMeta::tableExists should return false when the given table name is not found in the database.'));

	$this->isFalse($oMeta->tableExists('test2'),
		_WT('weePgSQLDbMeta::tableExists should return false when the given table name is found in the database but the table is invisible.'));

	// weePgSQLDbMeta::table

	try {
		$oMeta->table('lhc');
		$this->fail(_WT('weePgSQLDbMeta::table should throw an UnexpectedValueException when requesting a table which does not exist in the database.'));
	} catch (UnexpectedValueException $e) {}

	try {
		$oMeta->table('test2');
		$this->fail(_WT('weePgSQLDbMeta::table should throw an UnexpectedValueException when requesting an invisible table.'));
	} catch (UnexpectedValueException $e) {}

	try {
		$oMeta->table('pg_namespace');
	} catch (Exception $e) {
		$this->fail(sprintf(_WT('weePgSQLDbMeta::table throws a %s when requesting a visible table from a system catalog.'),
			get_class($e)));
	}

	$oTable = $oMeta->table('test1');

	// weePgSQLDbMetaTable::schemaName

	$this->isEqual($oCurrent->name(), $oTable->schemaName(),
		_WT('weePgSQLDbMeta::table does not return table from the correct schema.'));

	// weePgSQLDbMetaTable::name

	$this->isEqual('test1', $oTable->name(),
		_WT('weePgSQLDbMeta::table does not return the requested table.'));

	// weePgSQLDbMetaTable::comment

	$this->isEqual('Tests are marvelous.', $oTable->comment(),
		_WT('weePgSQLDbMetaTable::comment does not correctly return the comment of the table.'));
}
catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
