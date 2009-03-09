<?php

require(dirname(__FILE__) . '/../../mysql/connect.php.inc');
$oMeta = $oDb->meta();

try
{
	$oDb->query('CREATE TABLE IF NOT EXISTS test1 (a integer, b integer, c integer, CONSTRAINT i_am_a_pk PRIMARY KEY (c, a))');
	$oDb->query('CREATE TABLE IF NOT EXISTS test2 (a integer)');

	$oTable1 = $oMeta->table('test1');
	$oTable2 = $oMeta->table('test2');

	// weeMySQLDbMetaTable::hasPrimaryKey

	$this->isTrue($oTable1->hasPrimaryKey(),
		_WT('weeMySQLDbMetaTable::hasPrimaryKey should return true when the table has a primary key.'));

	$this->isFalse($oTable2->hasPrimaryKey(),
		_WT('weeMySQLDbMetaTable::hasPrimaryKey should return false when the table does not have a primary key.'));

	// weeMySQLDbMetaTable::primaryKey

	try {
		$oTable2->primaryKey();
		$this->fail(_WT('weeMySQLDbMetaTable::primaryKey should throw an IllegalStateException when the table does not have a primary key.'));
	} catch (IllegalStateException $e) {}

	$oPrimaryKey = $oTable1->primaryKey();

	// weeMySQLDbMetaPrimaryKey::name

	$this->isEqual('PRIMARY', $oPrimaryKey->name(),
		_WT('weeMySQLDbMetaPrimaryKey::name should return "PRIMARY".'));

	// weeMySQLDbMetaPrimaryKey::columnsNames

	$this->isEqual(array('a', 'c'), $oPrimaryKey->columnsNames(),
		_WT('weeMySQLDbMetaPrimaryKey::columnsNames does not correctly return all the columns of the primary key.'));

	// weeMySQLDbMetaPrimaryKey::tableName

	$this->isEqual('test1', $oPrimaryKey->tableName(),
		_WT('weeMySQLDbMetaPrimaryKey::tableName does not correctly return the name of the table of the primary key.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS test1');
$oDb->query('DROP TABLE IF EXISTS test2');
if (isset($oException))
	throw $oException;
