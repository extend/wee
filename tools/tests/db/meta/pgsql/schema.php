<?php

require(dirname(__FILE__) . '/../../pgsql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('BEGIN');

try
{
	$oDb->query('CREATE SCHEMA pikachu CREATE TABLE test1 () CREATE TABLE test2 ()');
	$oDb->query("COMMENT ON SCHEMA pikachu IS 'pika pika!'");

	// weePgSQLDbMeta::schemaExists

	$this->isTrue($oMeta->schemaExists('pikachu'),
		_WT('weePgSQLDbMeta::schemaExists should return true when the given schema name is found in the database.'));

	$this->isFalse($oMeta->schemaExists('dracaufeu'),
		_WT('weePgSQLDbMeta::schemaExists should return false when the given schema name is found in the database.'));

	// weePgSQLDbMeta::schema

	try {
		$oMeta->schema('dracaufeu');
		$this->fail(_WT('weePgSQLDbMeta::schema should throw an UnexpectedValueException when requesting a schema which does not exist.'));
	} catch (UnexpectedValueException $oIgnored) {}

	$oSchema = $oMeta->schema('pikachu');

	// weePgSQLDbMetaSchema::name

	$this->isEqual('pikachu', $oSchema->name(),
		_WT('weePgSQLDbMetaSchema does not return a correct name.'));

	// weePgSQLDbMetaSchema::tableExists

	$this->isTrue($oSchema->tableExists('test1'),
		_WT('weePgSQLDbMetaSchema::tableExists should return true when the given table name is found in the schema.'));

	$this->isFalse($oSchema->tableExists('test3'),
		_WT('weePgSQLDbMetaSchema::tableExists should return true when the given table name is found in the schema.'));

	// weePgSQLDbMetaSchema::table

	try {
		$oTable = $oSchema->table('not_found');
		$this->fail(_WT('weePgSQLDbMetaSchema::table should throw a UnexpectedValueException when requesting a table which does not exist in the schema.'));
	} catch (UnexpectedValueException $e) {}

	$oTable = $oSchema->table('test1');

	// weePgSQLDbMetaTable::schemaName

	$this->isEqual('pikachu', $oTable->schemaName(),
		_WT('Instances of weePgSQLDbMetaTable returned by weePgSQLDbMetaSchema does not return a correct schema name.'));

	// weePgSQLDbMetaTable::name

	$this->isEqual('test1', $oTable->name(),
		_WT('Instances of weePgSQLDbMetaTable returned by weePgSQLDbMetaSchema does not return a correct name.'));

	// weePgSQLDbMetaSchema::comment

	$this->isEqual('pika pika!', $oSchema->comment(),
		_WT('weePgSQLDbMetaSchema::comment does not correctly return the comment of the schema.'));

	// weePgSQLDbMetaSchema::tables

	$aNames = array();
	foreach ($oSchema->tables() as $oTable)
		$aNames[] = $oTable->name();
	$this->isEqual(array('test1', 'test2'), $aNames,
		_WT('weePgSQLDbMetaSchema::tables does not correctly return all the tables in the schema.'));
}
catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;

// weePgSQLDbMeta::currentSchema

$this->isEqual($oDb->queryValue('SELECT current_schema()'), $oMeta->currentSchema()->name(),
	_WT('weePgSQLDbMeta::currentSchema does not return the correct schema.'));
