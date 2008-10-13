<?php

require(dirname(__FILE__) . '/../../pgsql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('BEGIN');

try
{
	$oDb->query('CREATE TABLE test1 (a integer, b integer, c integer, CONSTRAINT i_am_a_pk PRIMARY KEY (c, a))');
	$oDb->query('CREATE TABLE test2 (a integer)');
	$oDb->query("COMMENT ON CONSTRAINT i_am_a_pk ON test1 IS 'not a player killer!'");

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

	$this->isEqual($oPrimaryKey->name(), 'i_am_a_pk',
		_('weePgSQLDbMetaPrimaryKey::name does not correctly return the name of the primary key.'));

	// weePgSQLDbMetaPrimaryKey::comment

	$this->isEqual($oPrimaryKey->comment(), 'not a player killer!',
		_('weePgSQLDbMetaPrimaryKey::comment does not correctly return the comment of the primary key.'));

	// weePgSQLDbMetaPrimaryKey::columns

	$this->isEqual($oPrimaryKey->columns(), array('c', 'a'),
		_('weePgSQLDbMetaPrimaryKey::columns does not correctly return all the columns of the primary key.'));
}
catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
