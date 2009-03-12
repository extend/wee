<?php

require(dirname(__FILE__) . '/../../oracle/connect.php.inc');
$oMeta = $oDb->meta();

// weeOracleDbMeta::schemas

$bSchemaFound	= false;
$aSchemasNames	= array();
foreach ($oMeta->schemas() as $oSchema) {
	if ($oSchema->name() == 'SYSTEM')
		$bSchemaFound = true;
	$aSchemasNames[] = $oSchema->name();
}

$this->isTrue($bSchemaFound,
	_WT('weeOracleDbMeta::schemas should return at least the SYSTEM schema.'));

// weeOracleDbMeta::schemasNames

$this->isEqual($aSchemasNames, $oMeta->schemasNames(),
	_WT("weeOracleDbMeta::schemasNames did not return the same schemas' names as the schemas method."));

// weeOracleDbMeta::schemaExists

$this->isTrue($oMeta->schemaExists('SYSTEM'),
	_WT('weeOracleDbMeta::schemaExists should return true when the given schema name is found in the database.'));

$this->isFalse($oMeta->schemaExists('dracaufeu'),
	_WT('weeOracleDbMeta::schemaExists should return false when the given schema name is found in the database.'));

// weeOracleDbMeta::schema

try {
	$oMeta->schema('dracaufeu');
	$this->fail(_WT('weeOracleDbMeta::schema should throw an UnexpectedValueException when requesting a schema which does not exist.'));
} catch (UnexpectedValueException $e) {}

$oSchema = $oMeta->schema('SYSTEM');

// weeOracleDbMetaSchema::name

$this->isEqual('SYSTEM', $oSchema->name(),
	_WT('weeOracleDbMetaSchema does not return a correct name.'));

// weeOracleDbMetaSchema::tableExists

$this->isTrue($oSchema->tableExists('HELP'),
	_WT('weeOracleDbMetaSchema::tableExists should return true when the given table name is found in the schema.'));

$this->isFalse($oSchema->tableExists('dracaufeu'),
	_WT('weeOracleDbMetaSchema::tableExists should return true when the given table name is found in the schema.'));

// weeOracleDbMetaSchema::table

try {
	$oTable = $oSchema->table('not_found');
	$this->fail(_WT('weeOracleDbMetaSchema::table should throw a UnexpectedValueException when requesting a table which does not exist in the schema.'));
} catch (UnexpectedValueException $e) {}

$oTable = $oSchema->table('HELP');

// weeOracleDbMetaTable::schemaName

$this->isEqual('SYSTEM', $oTable->schemaName(),
	_WT('Instances of weeOracleDbMetaTable returned by weeOracleDbMetaSchema does not return a correct schema name.'));

// weeOracleDbMetaTable::name

$this->isEqual('HELP', $oTable->name(),
	_WT('Instances of weeOracleDbMetaTable returned by weeOracleDbMetaSchema does not return a correct name.'));

// weeOracleDbMetaSchema::tables

$aNames = array();
foreach ($oSchema->tables() as $oTable)
	$aNames[] = $oTable->name();
$this->isTrue(in_array('HELP', $aNames),
	_WT('weeOracleDbMetaSchema::tables does not correctly return all the tables in the schema.'));

// weeOracleDbMetaSchema::tablesNames

$this->isEqual($aNames, $oSchema->tablesNames(),
	_WT("weeOracleDbMetaSchema::tablesNames does not return the expected tables' names."));

// weeOracleDbMeta::currentSchema

$this->isEqual($oDb->queryValue('SELECT USER FROM DUAL'), $oMeta->currentSchema()->name(),
	_WT('weeOracleDbMeta::currentSchema does not return the correct schema.'));
