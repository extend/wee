<?php

require(dirname(__FILE__) . '/../../pgsql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('START TRANSACTION');

try
{
	$oDb->query('CREATE SCHEMA schema_test CREATE TABLE test1 (a integer, b integer, PRIMARY KEY (b, a))');
	$oDb->query('CREATE TABLE test2 (a integer, b integer, c integer, CONSTRAINT i_am_a_fk FOREIGN KEY (c, b) REFERENCES schema_test.test1 (b, a))');
	$oDb->query("COMMENT ON CONSTRAINT i_am_a_fk ON test2 IS 'not a parricide!'");

	$oTable = $oMeta->table('test2');

	// weePgSQLDbMetaTable::foreignKeys

	$aForeignKeysNames = array();
	foreach ($oTable->foreignKeys() as $oForeignKey)
		$aForeignKeysNames[] = $oForeignKey->name();

	$this->isEqual(array('i_am_a_fk'), $aForeignKeysNames,
		_WT('weePgSQLDbMetaTable::foreignKeys did not return the expected foreign key.'));

	// weePgSQLDbMetaTable::foreignKeyExists

	$this->isTrue($oTable->foreignKeyExists('i_am_a_fk'),
		_WT('weePgSQLDbMetaTable::foreignKeyExists should return true when the given foreign key exists in the table.'));

	$this->isFalse($oTable->foreignKeyExists('key_which_does_not_exist'),
		_WT('weePgSQLDbMetaTable::hasPrimaryKey should return false when the table does not have a primary key.'));

	// weePgSQLDbMetaTable::foreignKey

	try {
		$oTable->foreignKey('key_which_does_not_exist');
		$this->fail(_WT('weePgSQLDbMetaTable::foreignKey should throw an UnexpectedValueException when the given foreign key does not exist.'));
	} catch (UnexpectedValueException $e) {}

	$oForeignKey = $oTable->foreignKey('i_am_a_fk');

	// weePgSQLDbMetaForeignKey::name

	$this->isEqual('i_am_a_fk', $oForeignKey->name(),
		_WT('weePgSQLDbMetaForeignKey::name does not correctly return the name of the foreign key.'));

	// weePgSQLDbMetaPrimaryKey::comment

	$this->isEqual('not a parricide!', $oForeignKey->comment(),
		_WT('weePgSQLDbMetaForeignKey::comment does not correctly return the comment of the foreign key.'));

	// weePgSQLDbMetaForeignKey::columnsNames

	$this->isEqual(array('c', 'b'), $oForeignKey->columnsNames(),
		_WT('weePgSQLDbMetaForeignKey::columnsNames does not correctly return all the columns of the foreign key.'));

	// weePgSQLDbMetaForeignKey::schemaName

	$this->isEqual($oMeta->currentSchema()->name(), $oForeignKey->schemaName(),
		_WT('weePgSQLDbMetaForeignKey::schemaName does not correctly return the name of the schema in which is the foreign key.'));

	// weePgSQLDbMetaForeignKey::referencedColumnsNames

	$this->isEqual(array('b', 'a'), $oForeignKey->referencedColumnsNames(),
		_WT('weePgSQLDbMetaForeignKey::referencedColumnsNames does not correctly return all the referenced columns of the foreign key.'));

	// weePgSQLDbMetaForeignKey::referencedSchemaName

	$this->isEqual('schema_test', $oForeignKey->referencedSchemaName(),
		_WT('weePgSQLDbMetaForeignKey::referencedSchemaName does not correctly return the name of the schema in which is the referenced table.'));

	// weePgSQLDbMetaForeignKey::referencedTableName

	$this->isEqual('test1', $oForeignKey->referencedTableName(),
		_WT('weePgSQLDbMetaForeignKey::referencedTableName does not correctly return the name of the referenced table.'));
}
catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
