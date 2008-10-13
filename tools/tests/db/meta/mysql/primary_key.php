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
		_('weePgSQLDbMetaTable::hasPrimaryKey should return true when the table has a primary key.'));

	$this->isFalse($oTable2->hasPrimaryKey(),
		_('weePgSQLDbMetaTable::hasPrimaryKey should return false when the table does not have a primary key.'));

	// weePgSQLDbMetaTable::primaryKey

	try {
		$oTable2->primaryKey();
		$this->fail(_('weePgSQLDbMetaTable::primaryKey should throw an IllegalStateException when the table does not have a primary key.'));
	} catch (IllegalStateException $e) {}

	$oPrimaryKey = $oTable1->primaryKey();

	// weePgSQLDbMetaPrimaryKey::name

	$this->isEqual($oPrimaryKey->name(), 'PRIMARY',
		_('weePgSQLDbMetaPrimaryKey::name should return "PRIMARY".'));

	// weePgSQLDbMetaPrimaryKey::columns

	$this->isEqual($oPrimaryKey->columns(), array('a', 'c'),
		_('weePgSQLDbMetaPrimaryKey::columns does not correctly return all the columns of the primary key.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS test1');
$oDb->query('DROP TABLE IF EXISTS test2');
if (isset($oException))
	throw $oException;
