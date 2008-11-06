<?php

require(dirname(__FILE__) . '/../../mysql/connect.php.inc');
$oMeta = $oDb->meta();

try
{
	$oDb->query('CREATE TABLE IF NOT EXISTS test1 (a integer, b integer, c integer, CONSTRAINT i_am_a_pk PRIMARY KEY (c, a))');
	$oDb->query('CREATE TABLE IF NOT EXISTS test2 (a integer)');

	$oTable1 = $oMeta->table('test1');
	$oTable2 = $oMeta->table('test2');

	// weePgSQLDbMetaTable::hasPrimaryKey

	$this->isTrue($oTable1->hasPrimaryKey(),
		_WT('weePgSQLDbMetaTable::hasPrimaryKey should return true when the table has a primary key.'));

	$this->isFalse($oTable2->hasPrimaryKey(),
		_WT('weePgSQLDbMetaTable::hasPrimaryKey should return false when the table does not have a primary key.'));

	// weePgSQLDbMetaTable::primaryKey

	try {
		$oTable2->primaryKey();
		$this->fail(_WT('weePgSQLDbMetaTable::primaryKey should throw an IllegalStateException when the table does not have a primary key.'));
	} catch (IllegalStateException $e) {}

	$oPrimaryKey = $oTable1->primaryKey();

	// weePgSQLDbMetaPrimaryKey::name

	$this->isEqual('PRIMARY', $oPrimaryKey->name(),
		_WT('weePgSQLDbMetaPrimaryKey::name should return "PRIMARY".'));

	// weePgSQLDbMetaPrimaryKey::columns

	$this->isEqual(array('a', 'c'), $oPrimaryKey->columns(),
		_WT('weePgSQLDbMetaPrimaryKey::columns does not correctly return all the columns of the primary key.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS test1');
$oDb->query('DROP TABLE IF EXISTS test2');
if (isset($oException))
	throw $oException;
