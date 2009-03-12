<?php

require(dirname(__FILE__) . '/../../oracle/connect.php.inc');
$oMeta = $oDb->meta();

try
{
	$oDb->query('CREATE TABLE "test" ("a" integer NOT NULL, "b" integer DEFAULT 42)');
	$oDb->query('COMMENT ON COLUMN "test"."a" IS ?', 'Column a');
	$oTable = $oMeta->table('test');

	// weeOracleDbMetaTable::columnExists

	$this->isTrue($oTable->columnExists('a'),
		_WT('weeOracleDbMetaTable::columnExists should return true when the given column name is found in the table.'));

	$this->isFalse($oTable->columnExists('c'),
		_WT('weeOracleDbMetaTable::columnExists should return false when the given column name is not found in the table.'));

	// weeOracleDbMetaTable::column

	try {
		$oTable->column('c');
		$this->fail(_WT('weeOracleDbMetaTable::column should throw an UnexpectedValueException when requesting a column which does not exist in the table.'));
	} catch (UnexpectedValueException $e) {}

	$oColumnA = $oTable->column('a');
	$oColumnB = $oTable->column('b');

	// weeMySQLDbMetaTable::columnsNames

	$this->isEqual(array('a', 'b'), $oTable->columnsNames(),
		_WT('weeMySQLDbMetaColumn::columnsNames does not correctly return the names of all the columns.'));

	// weeOracleDbMetaColumn::comment

	$this->isEqual('Column a', $oColumnA->comment(),
		_WT('weeOracleDbMetaColumn::comment does not correctly return the comment of the column.'));

	// weeOracleDbMetaColumn::hasDefault

	$this->isFalse($oColumnA->hasDefault(),
		_WT('weeOracleDbMetaColumn::hasDefaultValue should return false when the column does not have a default value.'));

	$this->isTrue($oColumnB->hasDefault(),
		_WT('weeOracleDbMetaColumn::hasDefaultValue should return true when the column has a default value.'));

	// weeOracleDbMetaColumn::defaultValue

	try {
		$oColumnA->defaultValue();
		$this->fail(_WT('weeOracleDbMetaColumn::defaultValue should throw an IllegalStateException when the column does not have a default value.'));
	} catch (IllegalStateException $e) {}

	$this->isEqual(42, $oColumnB->defaultValue(),
		_WT('weeOracleDbMetaColumn::defaultValue does not correctly return the default value of the column.'));

	// weeOracleDbMetaColumn::isNullable

	$this->isFalse($oColumnA->isNullable(),
		_WT('weeOracleDbMetaColumn::isNullable should return false when the column is not nullable.'));

	$this->isTrue($oColumnB->isNullable(),
		_WT('weeOracleDbMetaColumn::isNullable should return true when the column is nullable.'));

	// weeOracleDbMetaColumn::name

	$this->isEqual($oColumnA->name(), 'a',
		_WT('weeOracleDbMetaColumn::name does not correctly return the name of the column.'));

	// weeOracleDbMetaColumn::schemaName

	$this->isEqual($oMeta->currentSchema()->name(), $oColumnA->schemaName(),
		_WT('weeOracleDbMetaColumn::schemaName does not correctly return the name of the schema of the column.'));

	// weeOracleDbMetaColumn::tableName

	$this->isEqual('test', $oColumnA->tableName(),
		_WT('weeOracleDbMetaColumn::tableName does not correctly return the name of the table of the column.'));

	// weeOracleDbMetaTable::columns

	$aNames = array();
	foreach ($oTable->columns() as $oColumnA)
		$aNames[] = $oColumnA->name();
	$this->isEqual(array('a', 'b'), $aNames,
		_WT('weeOracleDbMetaTable::columns does not correctly return all the columns of the table.'));
}
catch (Exception $eException) {}

$oDb->query('DROP TABLE "test"');
if (isset($eException))
	throw $eException;
