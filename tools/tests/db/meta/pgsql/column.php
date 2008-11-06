<?php

require(dirname(__FILE__) . '/../../pgsql/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('BEGIN');
try
{
	$oDb->query('CREATE TABLE test (a integer NOT NULL, b integer DEFAULT 42)');
	$oDb->query("COMMENT ON COLUMN test.a IS 'Column a'");
	$oTable = $oMeta->table('test');

	// weePgSQLDbMetaTable::columnExists

	$this->isTrue($oTable->columnExists('a'),
		_WT('weePgSQLDbMetaTable::columnExists should return true when the given column name is found in the table.'));

	$this->isFalse($oTable->columnExists('c'),
		_WT('weePgSQLDbMetaTable::columnExists should return false when the given column name is not found in the table.'));

	// weePgSQLDbMetaTable::column

	try {
		$oTable->column('c');
		$this->fail(_WT('weePgSQLDbMetaTable::column should throw an UnexpectedValueException when requesting a column which does not exist in the table.'));
	} catch (UnexpectedValueException $e) {}

	$oColumnA = $oTable->column('a');
	$oColumnB = $oTable->column('b');

	// weePgSQLDbMetaColumn::comment

	$this->isEqual('Column a', $oColumnA->comment(),
		_WT('weePgSQLDbMetaColumn::comment does not correctly return the comment of the column.'));

	// weePgSQLDbMetaColumn::hasDefault

	$this->isFalse($oColumnA->hasDefault(),
		_WT('weePgSQLDbMetaColumn::hasDefaultValue should return false when the column does not have a default value.'));

	$this->isTrue($oColumnB->hasDefault(),
		_WT('weePgSQLDbMetaColumn::hasDefaultValue should return true when the column has a default value.'));

	// weePgSQLDbMetaColumn::defaultValue

	try {
		$oColumnA->defaultValue();
		$this->fail(_WT('weePgSQLDbMetaColumn::defaultValue should throw an IllegalStateException when the column does not have a default value.'));
	} catch (IllegalStateException $e) {}

	$this->isEqual(42, $oColumnB->defaultValue(),
		_WT('weePgSQLDbMetaColumn::defaultValue does not correctly return the default value of the column.'));

	// weePgSQLDbMetaColumn::isNullable

	$this->isFalse($oColumnA->isNullable(),
		_WT('weePgSQLDbMetaColumn::isNullable should return false when the column is not nullable.'));

	$this->isTrue($oColumnB->isNullable(),
		_WT('weePgSQLDbMetaColumn::isNullable should return true when the column is nullable.'));

	// weePgSQLDbMetaColumn::name

	$this->isEqual($oColumnA->name(), 'a',
		_WT('weePgSQLDbMetaColumn::name does not correctly return the name of the column.'));

	// weePgSQLDbMetaColumn::schemaName

	$this->isEqual('public', $oColumnA->schemaName(),
		_WT('weePgSQLDbMetaColumn::schemaName does not correctly return the name of the schema of the column.'));

	// weePgSQLDbMetaColumn::tableName

	$this->isEqual('test', $oColumnA->tableName(),
		_WT('weePgSQLDbMetaColumn::tableName does not correctly return the name of the table of the column.'));

	// weePgSQLDbMetaTable::columns

	$aNames = array();
	foreach ($oTable->columns() as $oColumnA)
		$aNames[] = $oColumnA->name();
	$this->isEqual(array('a', 'b'), $aNames,
		_WT('weePgSQLDbMetaTable::columns does not correctly return all the columns of the table.'));
}
catch (Exception $oException) {}

$oDb->query('ROLLBACK');
if (isset($oException))
	throw $oException;
