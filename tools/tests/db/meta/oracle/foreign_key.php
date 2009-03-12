<?php

require(dirname(__FILE__) . '/../../oracle/connect.php.inc');
$oMeta = $oDb->meta();
$sCurrentSchemaName = $oMeta->currentSchema()->name();

try
{
	$oDb->query('CREATE TABLE "test1" ("a" integer, "b" integer, PRIMARY KEY ("b", "a"))');
	$oDb->query('CREATE TABLE "test2" (
		"a" integer,
		"b" integer,
		"c" integer,
		CONSTRAINT "i_am_a_fk" FOREIGN KEY ("c", "b") REFERENCES "test1" ("b", "a")
	)');

	$oTable = $oMeta->table('test2');

	// weeOracleDbMetaTable::foreignKeys

	$aForeignKeysNames = array();
	foreach ($oTable->foreignKeys() as $oForeignKey)
		$aForeignKeysNames[] = $oForeignKey->name();

	$this->isEqual(array('i_am_a_fk'), $aForeignKeysNames,
		_WT('weeOracleDbMetaTable::foreignKeys did not return the expected foreign key.'));

	// weeOracleDbMetaTable::foreignKeyExists

	$this->isTrue($oTable->foreignKeyExists('i_am_a_fk'),
		_WT('weeOracleDbMetaTable::foreignKeyExists should return true when the given foreign key exists in the table.'));

	$this->isFalse($oTable->foreignKeyExists('key_which_does_not_exist'),
		_WT('weeOracleDbMetaTable::hasPrimaryKey should return false when the table does not have a primary key.'));

	// weeOracleDbMetaTable::foreignKey

	try {
		$oTable->foreignKey('key_which_does_not_exist');
		$this->fail(_WT('weeOracleDbMetaTable::foreignKey should throw an UnexpectedValueException when the given foreign key does not exist.'));
	} catch (UnexpectedValueException $e) {}

	$oForeignKey = $oTable->foreignKey('i_am_a_fk');

	// weeOracleDbMetaForeignKey::name

	$this->isEqual('i_am_a_fk', $oForeignKey->name(),
		_WT('weeOracleDbMetaForeignKey::name does not correctly return the name of the foreign key.'));

	// weeOracleDbMetaForeignKey::columnsNames

	$this->isEqual(array('c', 'b'), $oForeignKey->columnsNames(),
		_WT('weeOracleDbMetaForeignKey::columnsNames does not correctly return all the columns of the foreign key.'));

	// weeOracleDbMetaForeignKey::schemaName

	$this->isEqual($sCurrentSchemaName, $oForeignKey->schemaName(),
		_WT('weeOracleDbMetaForeignKey::schemaName does not correctly return the name of the schema in which is the foreign key.'));

	// weeOracleDbMetaForeignKey::referencedColumnsNames

	$this->isEqual(array('b', 'a'), $oForeignKey->referencedColumnsNames(),
		_WT('weeOracleDbMetaForeignKey::referencedColumnsNames does not correctly return all the referenced columns of the foreign key.'));

	// weeOracleDbMetaForeignKey::referencedSchemaName

	$this->isEqual($sCurrentSchemaName, $oForeignKey->referencedSchemaName(),
		_WT('weeOracleDbMetaForeignKey::referencedSchemaName does not correctly return the name of the schema in which is the referenced table.'));

	// weeOracleDbMetaForeignKey::referencedTableName

	$this->isEqual('test1', $oForeignKey->referencedTableName(),
		_WT('weeOracleDbMetaForeignKey::referencedTableName does not correctly return the name of the referenced table.'));
}
catch (Exception $eException) {}

$oDb->query('DROP TABLE "test2"');
$oDb->query('DROP TABLE "test1"');
if (isset($eException))
	throw $eException;
