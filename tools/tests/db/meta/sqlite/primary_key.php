<?php

require(dirname(__FILE__) . '/../../sqlite/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('CREATE TABLE test1 (a integer, b integer, c integer, PRIMARY KEY (c, a))');
$oDb->query('CREATE TABLE test2 (a integer)');

try
{
	$oTable1 = $oMeta->table('test1');
	$oTable2 = $oMeta->table('test2');

	// weeSQLiteDbMetaTable::hasPrimaryKey

	$this->isTrue($oTable1->hasPrimaryKey(),
		_WT('weeSQLiteDbMetaTable::hasPrimaryKey should return true when the table has a primary key.'));

	$this->isFalse($oTable2->hasPrimaryKey(),
		_WT('weeSQLiteDbMetaTable::hasPrimaryKey should return false when the table does not have a primary key.'));

	// weeSQLiteDbMetaTable::primaryKey

	try {
		$oTable2->primaryKey();
		$this->fail(_WT('weeSQLiteDbMetaTable::primaryKey should throw an IllegalStateException when the table does not have a primary key.'));
	} catch (IllegalStateException $e) {}

	$oPrimaryKey = $oTable1->primaryKey();

	// weeSQLiteDbMetaTable::primaryKeyColumnsNames

	try {
		$oTable2->primaryKeyColumnsNames();
		$this->fail(_WT('weeSQLiteDbMetaTable::primaryKeyColumnsNames should throw an IllegalStateException when the table does not have a primary key.'));
	} catch (IllegalStateException $e) {}

	$this->isEqual(array('a', 'c'), $oTable1->primaryKeyColumnsNames(),
		_WT('weeSQLiteDbMetaTable::primaryKeyColumnsNames does not correctly return all the columns of the primary key.'));

	// weeSQLiteDbMetaPrimaryKey::name

	$this->isNull($oPrimaryKey->name(),
		_WT('weeSQLiteDbMetaPrimaryKey::name should return null as a primary keys do not have a name in SQLite.'));

	// weeSQLiteDbMetaPrimaryKey::columnsNames

	$this->isEqual(array('a', 'c'), $oPrimaryKey->columnsNames(),
		_WT('weeSQLiteDbMetaPrimaryKey::columnsNames does not correctly return all the columns of the primary key.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE test1');
$oDb->query('DROP TABLE test2');
if (isset($oException))
	throw $oException;
