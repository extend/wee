<?php

require(dirname(__FILE__) . '/../../sqlite/connect.php.inc');
$oMeta = $oDb->meta();

$oDb->query('CREATE TABLE test1 (a integer, b integer, PRIMARY KEY (b, a))');
$oDb->query('CREATE TABLE test2 (a integer, b integer, c integer, FOREIGN KEY (c, b) REFERENCES test1 (b, a))');

try
{
	$oTable = $oMeta->table('test2');

	// weeSQLiteDbMetaTable::foreignKeys

	$aForeignKeysNames = array();
	foreach ($oTable->foreignKeys() as $oForeignKey)
		$aForeignKeysNames[] = $oForeignKey->name();

	$this->isEqual(array('0'), $aForeignKeysNames,
		_WT('weeSQLiteDbMetaTable::foreignKeys did not return the expected foreign key.'));

	// weeSQLiteDbMetaTable::foreignKeyExists

	$this->isTrue($oTable->foreignKeyExists('0'),
		_WT('weeSQLiteDbMetaTable::foreignKeyExists should return true when the given foreign key exists in the table.'));

	$this->isFalse($oTable->foreignKeyExists('1'),
		_WT('weeSQLiteDbMetaTable::hasPrimaryKey should return false when the table does not have a primary key.'));

	// weeSQLiteDbMetaTable::foreignKey

	try {
		$oTable->foreignKey('1');
		$this->fail(_WT('weeSQLiteDbMetaTable::foreignKey should throw an UnexpectedValueException when the given foreign key does not exist.'));
	} catch (UnexpectedValueException $e) {}

	$oForeignKey = $oTable->foreignKey('0');

	// weeSQLiteDbMetaForeignKey::name

	$this->isEqual('0', $oForeignKey->name(),
		_WT('weeSQLiteDbMetaForeignKey::name does not correctly return the name of the foreign key.'));

	// weeSQLiteDbMetaForeignKey::columnsNames

	$this->isEqual(array('c', 'b'), $oForeignKey->columnsNames(),
		_WT('weeSQLiteDbMetaForeignKey::columnsNames does not correctly return all the columns of the foreign key.'));

	// weeSQLiteDbMetaForeignKey::referencedColumnsNames

	$this->isEqual(array('b', 'a'), $oForeignKey->referencedColumnsNames(),
		_WT('weeSQLiteDbMetaForeignKey::referencedColumnsNames does not correctly return all the referenced columns of the foreign key.'));

	// weeSQLiteDbMetaForeignKey::referencedTableName

	$this->isEqual('test1', $oForeignKey->referencedTableName(),
		_WT('weeSQLiteDbMetaForeignKey::referencedTableName does not correctly return the name of the referenced table.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE test2');
$oDb->query('DROP TABLE test1');
if (isset($oException))
	throw $oException;
