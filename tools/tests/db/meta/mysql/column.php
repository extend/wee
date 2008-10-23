<?php

require(dirname(__FILE__) . '/../../mysql/connect.php.inc');
$oMeta = $oDb->meta();

try
{
	$oDb->query("CREATE TABLE IF NOT EXISTS dbmeta (a integer NOT NULL COMMENT 'Column a', b integer DEFAULT 42)");
	$oTable = $oMeta->table('dbmeta');

	// weeMySQLDbMetaTable::columnExists

	$this->isTrue($oTable->columnExists('a'),
		_('weeMySQLDbMetaTable::columnExists should return true when the given column name is found in the table.'));

	$this->isFalse($oTable->columnExists('c'),
		_('weeMySQLDbMetaTable::columnExists should return false when the given column name is not found in the table.'));

	// weeMySQLDbMetaTable::column

	try {
		$oTable->column('c');
		$this->fail(_('weeMySQLDbMetaTable::column should throw an UnexpectedValueException when requesting a column which does not exist in the table.'));
	} catch (UnexpectedValueException $e) {}

	$oColumnA = $oTable->column('a');
	$oColumnB = $oTable->column('b');

	// weeMySQLDbMetaColumn::comment

	$this->isEqual('Column a', $oColumnA->comment(),
		_('weeMySQLDbMetaColumn::comment does not correctly return the comment of the column.'));

	// weeMySQLDbMetaColumn::hasDefault

	$this->isFalse($oColumnA->hasDefault(),
		_('weeMySQLDbMetaColumn::hasDefaultValue should return false when the column does not have a default value.'));

	$this->isTrue($oColumnB->hasDefault(),
		_('weeMySQLDbMetaColumn::hasDefaultValue should return true when the column has a default value.'));

	// weeMySQLDbMetaColumn::defaultValue

	try {
		$oColumnA->defaultValue();
		$this->fail(_('weeMySQLDbMetaColumn::defaultValue should throw an IllegalStateException when the column does not have a default value.'));
	} catch (IllegalStateException $e) {}

	$this->isEqual(42, $oColumnB->defaultValue(),
		_('weeMySQLDbMetaColumn::defaultValue does not correctly return the default value of the column.'));

	// weeMySQLDbMetaColumn::isNullable

	$this->isFalse($oColumnA->isNullable(),
		_('weeMySQLDbMetaColumn::isNullable should return false when the column is not nullable.'));

	$this->isTrue($oColumnB->isNullable(),
		_('weeMySQLDbMetaColumn::isNullable should return true when the column is nullable.'));

	// weeMySQLDbMetaColumn::name

	$this->isEqual('a', $oColumnA->name(),
		_('weeMySQLDbMetaColumn::name does not correctly return the name of the column.'));

	// weeMySQLDbMetaColumn::tableName

	$this->isEqual('dbmeta', $oColumnA->tableName(),
		_('weeMySQLDbMetaColumn::tableName does not correctly return the name of the table of the column.'));

	// weeMySQLDbMetaTable::columns

	$aNames = array();
	foreach ($oTable->columns() as $oColumnA)
		$aNames[] = $oColumnA->name();
	$this->isEqual(array('a', 'b'), $aNames,
		_('weeMySQLDbMetaTable::columns does not correctly return all the columns of the table.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS dbmeta');
if (isset($oException))
	throw $oException;
