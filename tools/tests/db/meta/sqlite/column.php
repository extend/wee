<?php

require(dirname(__FILE__) . '/../../sqlite/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query("CREATE TABLE dbmeta (a integer NOT NULL, b integer DEFAULT 42)");

try
{
	$oTable = $oMeta->table('dbmeta');

	// weeSQLiteDbMetaTable::columnExists

	$this->isTrue($oTable->columnExists('a'),
		_WT('weeSQLiteDbMetaTable::columnExists should return true when the given column name is found in the table.'));

	$this->isFalse($oTable->columnExists('c'),
		_WT('weeSQLiteDbMetaTable::columnExists should return false when the given column name is not found in the table.'));

	// weeSQLiteDbMetaTable::column

	try {
		$oTable->column('c');
		$this->fail(_WT('weeSQLiteDbMetaTable::column should throw an UnexpectedValueException when requesting a column which does not exist in the table.'));
	} catch (UnexpectedValueException $e) {}

	$oColumnA = $oTable->column('a');
	$oColumnB = $oTable->column('b');

	// weeSQLiteDbMetaTable::columnsNames

	$this->isEqual(array('a', 'b'), $oTable->columnsNames(),
		_WT('weeSQLiteDbMetaColumn::columnsNames does not correctly return the names of all the columns.'));

	// weeSQLiteDbMetaColumn::hasDefault

	$this->isFalse($oColumnA->hasDefault(),
		_WT('weeSQLiteDbMetaColumn::hasDefaultValue should return false when the column does not have a default value.'));

	$this->isTrue($oColumnB->hasDefault(),
		_WT('weeSQLiteDbMetaColumn::hasDefaultValue should return true when the column has a default value.'));

	// weeSQLiteDbMetaColumn::defaultValue

	try {
		$oColumnA->defaultValue();
		$this->fail(_WT('weeSQLiteDbMetaColumn::defaultValue should throw an IllegalStateException when the column does not have a default value.'));
	} catch (IllegalStateException $e) {}

	$this->isEqual(42, $oColumnB->defaultValue(),
		_WT('weeSQLiteDbMetaColumn::defaultValue does not correctly return the default value of the column.'));

	// weeSQLiteDbMetaColumn::isNullable

	$this->isFalse($oColumnA->isNullable(),
		_WT('weeSQLiteDbMetaColumn::isNullable should return false when the column is not nullable.'));

	$this->isTrue($oColumnB->isNullable(),
		_WT('weeSQLiteDbMetaColumn::isNullable should return true when the column is nullable.'));

	// weeSQLiteDbMetaColumn::name

	$this->isEqual('a', $oColumnA->name(),
		_WT('weeSQLiteDbMetaColumn::name does not correctly return the name of the column.'));

	// weeSQLiteDbMetaColumn::tableName

	$this->isEqual('dbmeta', $oColumnA->tableName(),
		_WT('weeSQLiteDbMetaColumn::tableName does not correctly return the name of the table of the column.'));

	// weeSQLiteDbMetaTable::columns

	$aNames = array();
	foreach ($oTable->columns() as $oColumnA)
		$aNames[] = $oColumnA->name();
	$this->isEqual(array('a', 'b'), $aNames,
		_WT('weeSQLiteDbMetaTable::columns does not correctly return all the columns of the table.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE dbmeta');
if (isset($oException))
	throw $oException;
