<?php

require(dirname(__FILE__) . '/../../mssql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('BEGIN TRANSACTION');

try
{
	$oDb->query('CREATE SCHEMA schema_test CREATE TABLE test1 (a integer, b integer, PRIMARY KEY (b, a))');
	$oDb->query('CREATE TABLE test2 (a integer, b integer, c integer, CONSTRAINT i_am_a_fk FOREIGN KEY (c, b) REFERENCES schema_test.test1 (b, a))');

	$oTable = $oMeta->table('test2');

	// weeMSSQLDbMetaTable::foreignKeys

	$aForeignKeysNames = array();
	foreach ($oTable->foreignKeys() as $oForeignKey)
		$aForeignKeysNames[] = $oForeignKey->name();

	$this->isEqual(array('i_am_a_fk'), $aForeignKeysNames,
		_WT('weeMSSQLDbMetaTable::foreignKeys did not return the expected foreign key.'));

	// weeMSSQLDbMetaTable::foreignKeyExists

	$this->isTrue($oTable->foreignKeyExists('i_am_a_fk'),
		_WT('weeMSSQLDbMetaTable::foreignKeyExists should return true when the given foreign key exists in the table.'));

	$this->isFalse($oTable->foreignKeyExists('key_which_does_not_exist'),
		_WT('weeMSSQLDbMetaTable::hasPrimaryKey should return false when the table does not have a primary key.'));

	// weeMSSQLDbMetaTable::foreignKey

	try {
		$oTable->foreignKey('key_which_does_not_exist');
		$this->fail(_WT('weeMSSQLDbMetaTable::foreignKey should throw an UnexpectedValueException when the given foreign key does not exist.'));
	} catch (UnexpectedValueException $e) {}

	$oForeignKey = $oTable->foreignKey('i_am_a_fk');

	// weeMSSQLDbMetaForeignKey::name

	$this->isEqual('i_am_a_fk', $oForeignKey->name(),
		_WT('weeMSSQLDbMetaForeignKey::name does not correctly return the name of the foreign key.'));

	// weeMSSQLDbMetaForeignKey::columnsNames

	$this->isEqual(array('c', 'b'), $oForeignKey->columnsNames(),
		_WT('weeMSSQLDbMetaForeignKey::columnsNames does not correctly return all the columns of the foreign key.'));

	// weeMSSQLDbMetaForeignKey::schemaName

	$this->isEqual($oMeta->currentSchema()->name(), $oForeignKey->schemaName(),
		_WT('weeMSSQLDbMetaForeignKey::schemaName does not correctly return the name of the schema in which is the foreign key.'));

	// weeMSSQLDbMetaForeignKey::referencedColumnsNames

	$this->isEqual(array('b', 'a'), $oForeignKey->referencedColumnsNames(),
		_WT('weeMSSQLDbMetaForeignKey::referencedColumnsNames does not correctly return all the referenced columns of the foreign key.'));

	// weeMSSQLDbMetaForeignKey::referencedSchemaName

	$this->isEqual('schema_test', $oForeignKey->referencedSchemaName(),
		_WT('weeMSSQLDbMetaForeignKey::referencedSchemaName does not correctly return the name of the schema in which is the referenced table.'));

	// weeMSSQLDbMetaForeignKey::referencedTableName

	$this->isEqual('test1', $oForeignKey->referencedTableName(),
		_WT('weeMSSQLDbMetaForeignKey::referencedTableName does not correctly return the name of the referenced table.'));
}
catch (Exception $eException) {}

$oDb->query('ROLLBACK');
if (isset($eException))
	throw $eException;
