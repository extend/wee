<?php

require(dirname(__FILE__) . '/../../oracle/connect.php.inc');
$oMeta = $oDb->meta();

try
{
	$oDb->query('CREATE TABLE "test1" ("a" integer, "b" integer, "c" integer, CONSTRAINT "i_am_a_pk" PRIMARY KEY ("c", "a"))');
	$oDb->query('CREATE TABLE "test2" ("a" integer)');

	$oTable1 = $oMeta->table('test1');
	$oTable2 = $oMeta->table('test2');

	// weeOracleDbMetaTable::hasPrimaryKey

	$this->isTrue($oTable1->hasPrimaryKey(),
		_WT('weeOracleDbMetaTable::hasPrimaryKey should return true when the table has a primary key.'));

	$this->isFalse($oTable2->hasPrimaryKey(),
		_WT('weeOracleDbMetaTable::hasPrimaryKey should return false when the table does not have a primary key.'));

	// weeOracleDbMetaTable::primaryKey

	try {
		$oTable2->primaryKey();
		$this->fail(_WT('weeOracleDbMetaTable::primaryKey should throw an IllegalStateException when the table does not have a primary key.'));
	} catch (IllegalStateException $e) {}

	$oPrimaryKey = $oTable1->primaryKey();

	// weeOracleDbMetaPrimaryKey::name

	$this->isEqual('i_am_a_pk', $oPrimaryKey->name(),
		_WT('weeOracleDbMetaPrimaryKey::name does not correctly return the name of the primary key.'));

	// weeOracleDbMetaPrimaryKey::columnsNames

	$this->isEqual(array('c', 'a'), $oPrimaryKey->columnsNames(),
		_WT('weeOracleDbMetaPrimaryKey::columnsNames does not correctly return all the columns of the primary key.'));

	// weeOracleDbMetaPrimaryKey::schemaName

	$this->isEqual($oMeta->currentSchema()->name(), $oPrimaryKey->schemaName(),
		_WT('weeOracleDbMetaPrimaryKey::schemaName does not correctly return the name of the schema in which is the primary key.'));
}
catch (Exception $eException) {}

$oDb->query('DROP TABLE "test1"');
$oDb->query('DROP TABLE "test2"');
if (isset($eException))
	throw $eException;
