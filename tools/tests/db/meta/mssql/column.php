<?php

require(dirname(__FILE__) . '/../../mssql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('BEGIN TRANSACTION');

try
{
	$oDb->query('CREATE TABLE test (a integer, b integer NULL DEFAULT 42)');
	$oDb->query("
		EXEC sp_addextendedproperty
			MS_Description,	'across the river',
			'SCHEMA',		?,
			'TABLE',		test,
			'COLUMN',		a
	", $oMeta->currentSchema()->name());

	$oTable = $oMeta->table('test');

	// weeMSSQLDbMetaTable::columnExists

	$this->isTrue($oTable->columnExists('a'),
		_WT('weeMSSQLDbMetaTable::columnExists should return true when the given column name is found in the table.'));

	$this->isFalse($oTable->columnExists('c'),
		_WT('weeMSSQLDbMetaTable::columnExists should return false when the given column name is not found in the table.'));

	// weeMSSQLDbMetaTable::column

	try {
		$oTable->column('c');
		$this->fail(_WT('weeMSSQLDbMetaTable::column should throw an UnexpectedValueException when requesting a column which does not exist in the table.'));
	} catch (UnexpectedValueException $e) {}

	$oColumnA = $oTable->column('a');
	$oColumnB = $oTable->column('b');

	// weeMySQLDbMetaTable::columnsNames

	$this->isEqual(array('a', 'b'), $oTable->columnsNames(),
		_WT('weeMySQLDbMetaColumn::columnsNames does not correctly return the names of all the columns.'));

	// weeMSSQLDbMetaColumn::hasDefault

	$this->isFalse($oColumnA->hasDefault(),
		_WT('weeMSSQLDbMetaColumn::hasDefaultValue should return false when the column does not have a default value.'));

	$this->isTrue($oColumnB->hasDefault(),
		_WT('weeMSSQLDbMetaColumn::hasDefaultValue should return true when the column has a default value.'));

	// weeMSSQLDbMetaColumn::defaultValue

	try {
		$oColumnA->defaultValue();
		$this->fail(_WT('weeMSSQLDbMetaColumn::defaultValue should throw an IllegalStateException when the column does not have a default value.'));
	} catch (IllegalStateException $e) {}

	$this->isEqual(42, $oColumnB->defaultValue(),
		_WT('weeMSSQLDbMetaColumn::defaultValue does not correctly return the default value of the column.'));

	// weeMSSQLDbMetaColumn::isNullable

	$this->isFalse($oColumnA->isNullable(),
		_WT('weeMSSQLDbMetaColumn::isNullable should return false when the column is not nullable.'));

	$this->isTrue($oColumnB->isNullable(),
		_WT('weeMSSQLDbMetaColumn::isNullable should return true when the column is nullable.'));

	// weeMSSQLDbMetaColumn::name

	$this->isEqual($oColumnA->name(), 'a',
		_WT('weeMSSQLDbMetaColumn::name does not correctly return the name of the column.'));

	// weeMSSQLDbMetaColumn::schemaName

	$this->isEqual($oTable->schemaName(), $oColumnA->schemaName(),
		_WT('weeMSSQLDbMetaColumn::schemaName does not correctly return the name of the schema of the column.'));

	// weeMSSQLDbMetaColumn::tableName

	$this->isEqual('test', $oColumnA->tableName(),
		_WT('weeMSSQLDbMetaColumn::tableName does not correctly return the name of the table of the column.'));

	// weeMSSQLDbMetaTable::columns

	$aNames = array();
	foreach ($oTable->columns() as $oColumn)
		$aNames[] = $oColumn->name();
	$this->isEqual(array('a', 'b'), $aNames,
		_WT('weeMSSQLDbMetaTable::columns does not correctly return all the columns of the table.'));

	// weeMSSQLDbMetaComment::comment

	$this->isEqual('across the river', $oColumnA->comment(),
		_WT('weeMSSQLDbMeta::comment does not return the comment of the table.'));
}
catch (Exception $eException) {}

$oDb->query('ROLLBACK');
if (isset($eException))
	throw $eException;
