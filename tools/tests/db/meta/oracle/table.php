<?php

require(dirname(__FILE__) . '/../../oracle/connect.php.inc');
$oMeta		= $oDb->meta();
$oCurrent	= $oMeta->currentSchema();

try
{
	$oDb->query('CREATE TABLE "pikachu" ("a" INTEGER, "b" VARCHAR(42))');
	$oDb->query('CREATE TABLE HELP ("a" INTEGER)');
	$oDb->query('COMMENT ON TABLE "pikachu" IS ?', 'yellow pikachu is yellow');

	// weeOracleDbMeta::tables

	$iFoundTables = 0;
	$aTablesNames = array();
	foreach ($oMeta->tables() as $oTable)
	{
		if ($oTable->name() == 'pikachu')
			$iFoundTables++;
		elseif ($oTable->name() == 'HELP') {
			$this->isEqual($oCurrent->name(), $oTable->schemaName(),
				_WT('weeOracleDbMeta::tables did not return the table "HELP" from the current schema.'));
			$iFoundTables++;
		}

		$aTablesNames[] = $oTable->name();
	}

	$this->isEqual(2, $iFoundTables,
		_WT('weeOracleDbMeta::tables did not return the expected tables "test1" and "HELP".'));

	// weeOracleDbMeta::tablesNames

	$this->isEqual($aTablesNames, $oMeta->tablesNames(),
		_WT('weeOracleDbMeta::tablesNames did not return the same tables as the tables method.'));

	// weeOracleDbMeta::tableExists

	$this->isTrue($oMeta->tableExists('pikachu'),
		_WT('weeOracleDbMeta::tableExists should return true when the given table name is found in the database and the table is visible.'));

	$this->isFalse($oMeta->tableExists('not_found'),
		_WT('weeOracleDbMeta::tableExists should return false when the given table name is not found in the database.'));

	$this->isFalse($oMeta->tableExists('ALL_TABLES'),
		_WT('weeOracleDbMeta::tableExists should return false when the given table name is found in the database but the table is invisible.'));

	// weeOracleDbMeta::table

	try {
		$oMeta->table('lhc');
		$this->fail(_WT('weeOracleDbMeta::table should throw an UnexpectedValueException when requesting a table which does not exist in the database.'));
	} catch (UnexpectedValueException $e) {}

	try {
		$oMeta->table('ALL_COLUMNS');
		$this->fail(_WT('weeOracleDbMeta::table should throw an UnexpectedValueException when requesting an invisible table.'));
	} catch (UnexpectedValueException $e) {}

	$oTable = $oMeta->table('pikachu');

	// weeOracleDbMetaTable::schemaName

	$this->isEqual($oCurrent->name(), $oTable->schemaName(),
		_WT('weeOracleDbMeta::table does not return table from the correct schema.'));

	// weeOracleDbMetaTable::name

	$this->isEqual('pikachu', $oTable->name(),
		_WT('weeOracleDbMeta::table does not return the requested table.'));

	// weeOracleDbMetaTable::comment

	$this->isEqual('yellow pikachu is yellow', $oTable->comment(),
		_WT('weeOracleDbMetaTable::comment does not correctly return the comment of the table.'));
}
catch (Exception $eException) {}

$oDb->query('DROP TABLE "pikachu"');
$oDb->query('DROP TABLE HELP');
if (isset($eException))
	throw $eException;
