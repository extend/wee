<?php

require(dirname(__FILE__) . '/../../mssql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('BEGIN TRANSACTION');

try
{
	$oDb->query('CREATE TABLE test1 (a integer, b integer, c integer, CONSTRAINT i_am_a_pk PRIMARY KEY (c, a))');
	$oDb->query('CREATE TABLE test2 (a integer)');

	$oTable1 = $oMeta->table('test1');
	$oTable2 = $oMeta->table('test2');

	// weeMSSQLDbMetaTable::hasPrimaryKey

	$this->isTrue($oTable1->hasPrimaryKey(),
		_WT('weeMSSQLDbMetaTable::hasPrimaryKey should return true when the table has a primary key.'));

	$this->isFalse($oTable2->hasPrimaryKey(),
		_WT('weeMSSQLDbMetaTable::hasPrimaryKey should return false when the table does not have a primary key.'));

	// weeMSSQLDbMetaTable::primaryKey

	try {
		$oTable2->primaryKey();
		$this->fail(_WT('weeMSSQLDbMetaTable::primaryKey should throw an IllegalStateException when the table does not have a primary key.'));
	} catch (IllegalStateException $e) {}

	$oPrimaryKey = $oTable1->primaryKey();

	// weeMSSQLDbMetaPrimaryKey::name

	$this->isEqual('i_am_a_pk', $oPrimaryKey->name(),
		_WT('weeMSSQLDbMetaPrimaryKey::name does not correctly return the name of the primary key.'));

	// weeMSSQLDbMetaPrimaryKey::columnsNames

	$this->isEqual(array('c', 'a'), $oPrimaryKey->columnsNames(),
		_WT('weeMSSQLDbMetaPrimaryKey::columnsNames does not correctly return all the columns of the primary key.'));

	// weeMSSQLDbMetaPrimaryKey::schemaName

	$this->isEqual($oMeta->currentSchema()->name(), $oPrimaryKey->schemaName(),
		_WT('weeMSSQLDbMetaPrimaryKey::schemaName does not correctly return the name of the schema in which is the primary key.'));
}
catch (Exception $eException) {}

$oDb->query('ROLLBACK');
if (isset($eException))
	throw $eException;
