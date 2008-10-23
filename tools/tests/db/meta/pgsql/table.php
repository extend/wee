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

	// weePgSQLDbMeta::tableExists

	$this->isTrue($oMeta->tableExists('test1'),
		_('weePgSQLDbMeta::tableExists should return true when the given table name is found in the database and the table is visible.'));

	$this->isFalse($oMeta->tableExists('not_found'),
		_('weePgSQLDbMeta::tableExists should return false when the given table name is not found in the database.'));

	$this->isFalse($oMeta->tableExists('test2'),
		_('weePgSQLDbMeta::tableExists should return false when the given table name is found in the database but the table is invisible.'));

	// weePgSQLDbMeta::table

	try {
		$oMeta->table('lhc');
		$this->fail('weePgSQLDbMeta::table should throw an UnexpectedValueException when requesting a table which does not exist in the database.');
	} catch (UnexpectedValueException $e) {}

	try {
		$oMeta->table('test2');
		$this->fail(_('weePgSQLDbMeta::table should throw an UnexpectedValueException when requesting an invisible table.'));
	} catch (UnexpectedValueException $e) {}

	try {
		$oMeta->table('pg_namespace');
	} catch (Exception $e) {
		$this->fail(sprintf(_('weePgSQLDbMeta::table throw a %s when requesting a visible table from a system catalog.'),
			get_class($e)));
	}

	$oTable = $oMeta->table('test1');

	// weePgSQLDbMetaTable::schemaName

	$this->isEqual($oCurrent->name(), $oTable->schemaName(),
		_('weePgSQLDbMeta::table does not return table from the correct schema.'));

	// weePgSQLDbMetaTable::name

	$this->isEqual('test1', $oTable->name(),
		_('weePgSQLDbMeta::table does not return the requested table.'));

	// weePgSQLDbMetaTable::comment

	$this->isEqual('Tests are marvelous.', $oTable->comment(),
		_('weePgSQLDbMetaTable::comment does not correctly return the comment of the table.'));
}
catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
