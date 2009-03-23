<?php

require(dirname(__FILE__) . '/../../mssql/connect.php.inc');
$oMeta = $oDb->meta();

// weeMSSQLDbMeta::currentSchema

$this->isEqual($oDb->queryValue("SELECT COALESCE(SCHEMA_NAME(), 'dbo')"), $oMeta->currentSchema()->name(),
	_WT('weeMSSQLDbMeta::currentSchema does not return the correct schema.'));

$oDb->query('BEGIN TRANSACTION');

try
{
	$oDb->query('CREATE SCHEMA pikachu CREATE TABLE test1 (a int) CREATE TABLE test2 (a int)');
	$oDb->query("EXEC sp_addextendedproperty MS_Description, 'pika pika', 'SCHEMA', pikachu");

	// weeMSSQLDbMeta::schemas

	$bSchemaFound	= false;
	$aSchemasNames	= array();
	foreach ($oMeta->schemas() as $oSchema) {
		if ($oSchema->name() == 'pikachu')
			$bSchemaFound = true;
		$aSchemasNames[] = $oSchema->name();
	}

	$this->isTrue($bSchemaFound,
		_WT('weeMSSQLDbMeta::schemas should return at least the newly-created schema.'));

	// weeMSSQLDbMeta::schemasNames

	$this->isEqual($aSchemasNames, $oMeta->schemasNames(),
		_WT("weeMSSQLDbMeta::schemasNames did not return the same schemas' names as the schemas method."));

	// weeMSSQLDbMeta::schemaExists

	$this->isTrue($oMeta->schemaExists('pikachu'),
		_WT('weeMSSQLDbMeta::schemaExists should return true when the given schema name is found in the database.'));

	$this->isFalse($oMeta->schemaExists('dracaufeu'),
		_WT('weeMSSQLDbMeta::schemaExists should return false when the given schema name is found in the database.'));

	// weeMSSQLDbMeta::schema

	try {
		$oMeta->schema('dracaufeu');
		$this->fail(_WT('weeMSSQLDbMeta::schema should throw an UnexpectedValueException when requesting a schema which does not exist.'));
	} catch (UnexpectedValueException $oIgnored) {}

	$oSchema = $oMeta->schema('pikachu');

	// weeMSSQLDbMetaSchema::name

	$this->isEqual('pikachu', $oSchema->name(),
		_WT('weeMSSQLDbMetaSchema does not return a correct name.'));

	// weeMSSQLDbMetaSchema::comment

	$this->isEqual('pika pika', $oSchema->comment(),
		_WT('weeMSSQLDbMetaSchema::comment does not return the expected comment.'));

	// weeMSSQLDbMetaSchema::tableExists

	$this->isTrue($oSchema->tableExists('test1'),
		_WT('weeMSSQLDbMetaSchema::tableExists should return true when the given table name is found in the schema.'));

	$this->isFalse($oSchema->tableExists('test3'),
		_WT('weeMSSQLDbMetaSchema::tableExists should return true when the given table name is found in the schema.'));

	// weeMSSQLDbMetaSchema::table

	try {
		$oTable = $oSchema->table('not_found');
		$this->fail(_WT('weeMSSQLDbMetaSchema::table should throw a UnexpectedValueException when requesting a table which does not exist in the schema.'));
	} catch (UnexpectedValueException $e) {}

	$oTable = $oSchema->table('test1');

	// weeMSSQLDbMetaTable::schemaName

	$this->isEqual('pikachu', $oTable->schemaName(),
		_WT('Instances of weeMSSQLDbMetaTable returned by weeMSSQLDbMetaSchema does not return a correct schema name.'));

	// weeMSSQLDbMetaTable::name

	$this->isEqual('test1', $oTable->name(),
		_WT('Instances of weeMSSQLDbMetaTable returned by weeMSSQLDbMetaSchema does not return a correct name.'));

	// weeMSSQLDbMetaSchema::tables

	$aNames = array();
	foreach ($oSchema->tables() as $oTable)
		$aNames[] = $oTable->name();
	$this->isEqual(array('test1', 'test2'), $aNames,
		_WT('weeMSSQLDbMetaSchema::tables does not correctly return all the tables in the schema.'));

	// weeMSSQLDbMetaSchema::tablesNames

	$this->isEqual(array('test1', 'test2'), $oSchema->tablesNames(),
		_WT("weeMSSQLDbMetaSchema::tablesNames does not return the expected tables' names."));
}
catch (Exception $eException) {}

$oDb->query('ROLLBACK');
if (isset($eException))
	throw $eException;
